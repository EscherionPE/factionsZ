<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class PromoteSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.promote");
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
        $promoted_name = $args[0];
        $player_name = $sender->getName();
        
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        
        if (!$this->getPlugin()->isInFaction($promoted_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_not_in_fac", [$promoted_name]));
            return true;
        }
        
        if ($this->getPlugin()->getFaction($promoted_name) != $faction_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_same_fac", [$promoted_name]));
            return true;
        }
    
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.must_be_leader"));
            return true;
        }
        if($this->getPlugin()->getRank($promoted_name) == "Officer")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_ofc", [$promoted_name]));
            return true;
        }
        $this->getPlugin()->setRank($promoted_name, "Officer");
        if(($promoted = $this->getPlugin()->getServer()->getPlayerExact($promoted_name)) instanceof Player)
        {
            $promoted->sendMessage(TextFormat::GREEN . $this->translateString("promote.promoted", [$player_name]));
        }
        
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("promote.success", [$promoted_name]));
    
        return true;
    }
}