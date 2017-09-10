<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class DenySubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.deny");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        if (!empty($args))
        {
            return false;
        }
        
        $player_name = $sender->getName();
        
        $inv_timeout = $this->getPlugin()->getConfig()->get("inv_timeout");
        if(!isset($this->getPlugin()->inv_requests[$player_name]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_invited", [$inv_timeout]));
            return true;
        }
        
        $invitor_name = $this->getPlugin()->inv_requests[$player_name]["invitedby"];
        
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("deny.success"));
            
        if ( ($invitor = $this->getPlugin()->getServer()->getPlayerExact($invitor_name)) instanceof Player)
        {
            $invitor->sendMessage(TextFormat::RED . $this->translateString("deny.denied", [$player_name]));
        }
        unset($this->getPlugin()->inv_requests[$player_name]);
        return true;
    }
}