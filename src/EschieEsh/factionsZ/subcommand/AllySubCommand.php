<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class AllySubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.ally");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        if(empty($args))
        {
            return false;
        }
        $player_name = $sender->getName();
		$ally_limit = $this->getPlugin()->getConfig()->get("ally_limit");
        $ally_timeout = $this->getPlugin()->getConfig()->get("ally_timeout");
        if(!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if ($faction_name == $args[0])
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.your_own_fac"));
            return true;
        }
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.low_rank"));
            return true;
        }
        if(!$this->getPlugin()->factionExists($args[0]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.fac_doesnt_exst", [$args[0]]));
            return true;
        }
        $leader_name = $this->getPlugin()->getLeader($args[0]);
        
        if(isset($this->getPlugin()->ally_requests[$leader_name]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_has_request"));
            return true;
        }
        if($this->getPlugin()->areAllies($faction_name, $args[0]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.factions_already_allied", [$args[0]]));
            return true;
        }
        $leader = $sender->getServer()->getPlayerExact($leader_name);
        if (!($leader instanceof Player))
        {
            
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.leader_offline", [$args[0]]));
            return true;
        }
        if($this->getPlugin()->countAllies($args[0]) == $ally_limit)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.max_allies", [$args[0]]));
            return true;
        }
        if($this->getPlugin()->countAllies($faction_name) == $ally_limit)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.max_allies", [$faction_name]));
            return true;
        }
        $this->getPlugin()->ally_requests[$leader_name] = ["faction" => $faction_name, "requestedby" => $player_name];
        $this->getPlugin()->requestAlliance($leader_name, $ally_timeout);
        $leader->sendMessage(TextFormat::GREEN . $this->translateString("ally.requested", [$faction_name, $ally_timeout]));
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("ally.success", [$args[0]]));
        return true;
        
    }
}