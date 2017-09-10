<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\command\CommandSender;

class HomeSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.home");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        if(!empty($args))
        {
            return false;
        }
        $player_name = $sender->getName();
        if(!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if(!$this->getPlugin()->hasHome($faction_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.home_not_set"));
            return true;
        }
        $coords = $this->getPlugin()->getHomeCoords($faction_name);
        $sender->teleport(new Position($coords["homeX"], $coords["homeY"], $coords["homeZ"], $sender->getServer()->getLevelByName($coords["homeWorld"])));
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("home.success"));
        return true;
        
    }
}