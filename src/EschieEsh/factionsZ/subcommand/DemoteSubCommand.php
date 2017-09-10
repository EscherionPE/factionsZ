<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class DemoteSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.demote");
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
        $demoted_name = $args[0];
        $player_name = $sender->getName();
        
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if (!$this->getPlugin()->isInFaction($demoted_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_not_in_fac", [$demoted_name]));
            return true;
        }
        if ($this->getPlugin()->getFaction($demoted_name) != $faction_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_same_fac", [$demoted_name]));
            return true;
        }
    
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.must_be_leader"));
            return true;
        }
        if($this->getPlugin()->getRank($demoted_name) == "Member")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_mem", [$demoted_name]));
            return true;
        }
        $this->getPlugin()->setRank($demoted_name, "Member");
        if(($demoted = $this->getPlugin()->getServer()->getPlayerExact($demoted_name)) instanceof Player)
        {
            $demoted->sendMessage(TextFormat::RED . $this->translateString("demote.demoted", [$player_name]));
        }
        
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("demote.success", [$demoted_name]));
    
        return true;
    }
}