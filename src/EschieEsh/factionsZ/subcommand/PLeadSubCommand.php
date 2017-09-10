<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class PLeadSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.plead");
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
        $passedto_name = $args[0];
        if($player_name == $passedto_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.yourself"));
            return true;
        }
        if(!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.must_be_leader"));
            return true;
        }
        if (!$this->getPlugin()->isInFaction($passedto_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_not_in_fac", [$passedto_name]));
            return true;
        }
        if($this->getPlugin()->getFaction($passedto_name) != $faction_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_same_fac", [$passedto_name]));
            return true;
        }
        $this->getPlugin()->passLeadership($player_name, $faction_name, $passedto_name);
        if ( ($passedto = $this->getPlugin()->getServer()->getPlayerExact($passedto_name)) instanceof Player )
        {
            $passedto->sendMessage(TextFormat::GREEN . $this->translateString("plead.passed", [$player_name, $faction_name]));
        }
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("plead.success", [$passedto_name]));
        return true;
        
    }
}