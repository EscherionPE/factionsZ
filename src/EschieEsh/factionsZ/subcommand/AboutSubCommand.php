<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat as T;
use pocketmine\command\CommandSender;

class AboutSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender->hasPermission("factionsZ.command.about");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        $sender->sendMessage(T::ITALIC . T::GREEN . "factionsZ_v1.0.0" . T::AQUA . " created by " . T::YELLOW . "Panda843");
        return true;
    }
}