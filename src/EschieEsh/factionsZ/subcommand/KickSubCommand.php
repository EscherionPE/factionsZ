<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class KickSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.kick");
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
        $kicked_name = $args[0];
        if ($player_name == $kicked_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.yourself"));
            return true;
        }
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_not_in_fac", [$kicked_name]));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if (!$this->getPlugin()->isInFaction($kicked_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_not_in_fac"));
            return true;
        }
        if($this->getPlugin()->getFaction($kicked_name) != $faction_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_same_fac", [$kicked_name]));
            return true;
        }
        
        $player_rank = $this->getPlugin()->getRank($player_name);
        $kicked_rank = $this->getPlugin()->getRank($kicked_name);
        
        if($player_rank == "Member" || ($player_rank == "Officer" && ($kicked_rank == "Officer" || $kicked_rank == "Leader")))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.lowrank"));
            return true;
        }
        
        if ( ($kicked = $this->getPlugin()->getServer()->getPlayerExact($kicked_name)) instanceof Player)
        {
            $kicked->sendMessage(TextFormat::RED . $this->translateString("kick.kicked", [$faction_name, $player_name]));
        }
        
        $this->getPlugin()->kickPlayer($kicked_name, $faction_name);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("kick.success", [$kicked_name]));
        return true;
    }
}