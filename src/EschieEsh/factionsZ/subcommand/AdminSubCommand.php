<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class AdminSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.admin");
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
        
        if(isset($this->getPlugin()->is_admin[$player_name]))
        {
            unset($this->getPlugin()->is_admin[$player_name]);
            $sender->sendMessage(TextFormat::GREEN . $this->translateString("admin.off"));
            return true;
        }
        $this->getPlugin()->is_admin[$player_name] = true;
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("admin.on"));
        return true;
    }
}