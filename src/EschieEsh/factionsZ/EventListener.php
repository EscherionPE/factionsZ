<?php
namespace EschieEsh\factionsZ;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EventListener implements Listener
{
	/** @var factionsZ */
	private $plugin;
    
	public function __construct(factionsZ $plugin)
	{
		$this->plugin = $plugin;
	}
    
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param PlayerChatEvent $event
	*/
	public function factionChat(PlayerChatEvent $event) 
	{
		$msg = $event->getMessage();
		$player = $event->getPlayer();
		$player_name = $player->getName();
        
		if($this->plugin->isInFaction($player_name))
		{
			$faction_name = $this->plugin->getFaction($player_name);
			if(isset($this->plugin->faction_chats[$player_name]["faction"]))
			{
				$event->setCancelled(true);
				foreach($this->plugin->getServer()->getOnlinePlayers() as $factionPlayer)
				{
					$factionPlayer_name = $factionPlayer->getName();
					if($this->plugin->isInFaction($factionPlayer_name) && $this->plugin->getFaction($factionPlayer_name) == $faction_name)
					{
						$message = $this->plugin->getLanguage()->translateString("faction.chat", [$player_name, $msg]);
						$factionPlayer->sendMessage($message);
						
					}
				}
			}
        
			elseif(isset($this->plugin->faction_chats[$player_name]["ally"]))
			{
				$event->setCancelled(true);
				foreach($this->plugin->getServer()->getOnlinePlayers() as $factionPlayer)
				{
					$factionPlayer_name = $factionPlayer->getName();
					if($this->plugin->isInFaction($factionPlayer_name))
					{
						$factionPlayer_faction_name = $this->plugin->getFaction($factionPlayer_name);
						if($this->plugin->areAllies($factionPlayer_faction_name, $faction_name))
						{
							$message = $this->plugin->getLanguage()->translateString("faction.achat", [$faction_name, $player_name, $msg]);
							$factionPlayer->sendMessage($message);
						}
					}
				}
				$message = $this->plugin->getLanguage()->translateString("faction.achat", [$faction_name, $player_name, $msg]);
				$player->sendMessage($message);
			}
		}
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param BlockPlaceEvent $event
	*/
	public function onBlockPlace(BlockPlaceEvent $event)
	{
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$player_name = $player->getName();
        
		$block_data = ["x" => $block->getX(), "z" => $block->getZ(), "world" => $block->getLevel()->getName()];
		if(($faction = $this->plugin->getFactionByClaim($block_data)) != null)
		{
			if(!($this->plugin->isInFaction($player_name) && $this->plugin->getFaction($player_name) == $faction))
			{
				$player->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->translateString("error.already_claimed", [$faction]));
				$event->setCancelled(true);
			}
		}
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param BlockBreakEvent $event
	*/
	public function onBlockBreak(BlockBreakEvent $event)
	{
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$player_name = $player->getName();
		$block_data = ["x" => $block->getX(), "z" => $block->getZ(), "world" => $block->getLevel()->getName()];
		if(!isset($this->plugin->is_admin[$player_name]) && ($faction = $this->plugin->getFactionByClaim($block_data)) != null)
		{
			if(!($this->plugin->isInFaction($player_name) && $this->plugin->getFaction($player_name) == $faction))
			{
				$player->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->translateString("error.already_claimed", [$faction]));
				$event->setCancelled(true);
			}
		}
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param PlayerMoveEvent $event
	*/
	public function onPlayerMove(PlayerMoveEvent $event)
	{
		$player = $event->getPlayer();
		$player_name = $player->getName();
		$coords = ["x" => $player->getFloorX(), "z" => $player->getFloorZ(), "world" => $player->getLevel()->getName()];
        
		if(($faction = $this->plugin->getFactionByClaim($coords)) != null)
		{
			if($this->plugin->claims[$player_name] != $faction)
			{
				if($this->plugin->isInFaction($player_name))
				{
					$faction_name = $this->plugin->getFaction($player_name);
					if($faction_name == $faction)
					{
						$player->addTitle("§o§b$faction", "§o§7-= Home =-", 20, 100, 30);
					} 
					elseif (in_array($faction, $this->plugin->getAllies($faction_name)))
					{
						$player->addTitle("§o§b$faction", "§o§7-= Ally =-", 20, 100, 30);
					}
					else
					{
						$player->addTitle("§o§c$faction", "§o§7-= Beware =-", 20, 100, 30);
					}
				} 
				else 
				{
					$player->addTitle("§o§c$faction", "§o§7-= Beware =-", 20, 100, 30);
				}
				$this->plugin->claims[$player_name] = $faction;
			}
		} 
		else 
		{
			if($this->plugin->claims[$player_name] != "Wilderness")
			{
				$player->addTitle("§o§aWilderness", "", 20, 60, 20);
				$this->plugin->claims[$player_name] = "Wilderness";
			}
		}
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param EntityDamageEvent $event
	*/
	public function factionPVP(EntityDamageEvent $event) 
	{
		if($event instanceof EntityDamageByEntityEvent) 
		{
			$victim = $event->getEntity();
			$damager = $event->getDamager();
			if(!($victim instanceof Player) or !($damager instanceof Player)) 
			{
				return true;
			}
			$victim_name = $victim->getName();
			$damager_name = $damager->getName();
			if(!($this->plugin->isInFaction($victim_name) && $this->plugin->isInFaction($damager_name)))
			{
				return true;
			}
			$f1 = $this->plugin->getFaction($victim_name);
			$f2 = $this->plugin->getFaction($damager_name);
			if($f1 == $f2 || $this->plugin->areAllies($f1,$f2)) 
			{
				$event->setCancelled(true);
			}
		}
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param EntityDamageEvent $event
	*/
	public function onPlayerDestruction(PlayerDeathEvent $event) 
	{
		$player = $event->getEntity();
		$cause = $player->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent)
		{
			$killer = $cause->getDamager();
			if($killer instanceof Player)
			{
				$killer_name = $killer->getName();
				$player_name = $player->getName();
				if($this->plugin->isInFaction($killer_name))
				{
					$faction_name = $this->plugin->getFaction($killer_name);
					if($this->plugin->isInFaction($player_name))
					{
						$this->plugin->setStrength($faction_name, $this->plugin->getStrength($faction_name) + $this->plugin->getConfig()->get("victim_in_faction"));
					} 
					else 
					{
						$this->plugin->setStrength($faction_name, $this->plugin->getStrength($faction_name) + $this->plugin->getConfig()->get("victim_not_in_faction"));
					}
				}
				if($this->plugin->isInFaction($player_name))
				{
					$faction_name = $this->plugin->getFaction($player_name);
					if($this->plugin->isInFaction($killer_name))
					{      
						$this->plugin->setStrength($faction_name,$this->plugin->getStrength($faction_name) - $this->plugin->getConfig()->get("killer_in_faction"));
					} 
					else 
					{
						$this->plugin->setStrength($faction_name,$this->plugin->getStrength($faction_name) - $this->plugin->getConfig()->get("killer_not_in_faction"));
					}
				}    
			}
		}   
	}
	/**
	* @ignoreCancelled false
	* @priority LOWEST
	*
	* @param PlayerJoinEvent $event
	*/
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$this->plugin->claims[$event->getPlayer()->getName()] = "None";
	}
}