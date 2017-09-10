<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat as T;
use pocketmine\command\CommandSender;

class InfoSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender->hasPermission("factionsZ.command.info");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        $player_name = $sender->getName();
        
        if(empty($args))
        {
            if(!$this->getPlugin()->isInFaction($player_name))
            {
                $sender->sendMessage(T::RED . $this->translateString("error.not_in_fac"));
                return true;
            }
            $faction_name = $this->getPlugin()->getFaction($player_name);
        } else {
            $faction_name = $args[0];
            if(!$this->getPlugin()->factionExists($faction_name))
            {
                $sender->sendMessage(T::RED . $this->translateString("error.fac_doesnt_exst", [$faction_name]));
                return true;
            }
        }
        
        $faction_info = $this->getPlugin()->getFactionInfo($faction_name);
        if(empty($faction_info["members"]))
        {
            $members = "None";
        }
        else 
        {
            $members = "§a";
            foreach($faction_info["members"] as $member)
            {
                $members .= "{$member}§2|§a";
            }
        }
        if(empty($faction_info["officers"]))
        {
            $officers = "None";
        } 
        else 
        {
            $officers = "§a";
            foreach($faction_info["officers"] as $officer)
            {
                $officers .= "{$officer}§2|§a";
            }
        }
        if(empty($faction_info["allies"]))
        {
            $allies = "None";
        } 
        else 
        {
            $allies = "§b";
            foreach($faction_info["allies"] as $ally)
            {
                $allies .= "{$ally}§3|§b";
            }
        }
        $sender->getServer()->getPlayerExact($faction_info["leader"]) instanceof Player ? $status=T::GREEN."ON" : $status=T::RED."OFF";
        $sender->sendMessage($this->translateString("info.layout.top", [$faction_name]));
        $sender->sendMessage($this->translateString("info.layout.mid1", [$faction_info["amount_players"], 50]));
        $sender->sendMessage($this->translateString("info.layout.mid2", [$faction_info["leader"], $status]));
        $sender->sendMessage($this->translateString("info.layout.mid3", [$faction_info["strength"]]));
        $sender->sendMessage($this->translateString("info.layout.mid4", [$allies]));
        $sender->sendMessage($this->translateString("info.layout.mid5"));
        $sender->sendMessage($this->translateString("info.layout.mid6", [$officers]));
        $sender->sendMessage($this->translateString("info.layout.mid7", [$members]));
        $sender->sendMessage($this->translateString("info.layout.mid8"));
        $sender->sendMessage($this->translateString("info.layout.mid9", [$faction_info["description"]]));
        $sender->sendMessage($this->translateString("info.layout.bottom"));
        return true;
        
    }
}