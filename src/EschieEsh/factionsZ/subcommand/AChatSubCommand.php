<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class AChatSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.achat");
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
        if(isset($this->getPlugin()->faction_chats[$player_name]["ally"]))
        {
            unset($this->getPlugin()->faction_chats[$player_name]["ally"]);
            $sender->sendMessage(TextFormat::GREEN . $this->translateString("ac.off"));
            return true;
        }
        $this->getPlugin()->faction_chats[$player_name]["ally"] = true;
        unset($this->getPlugin()->faction_chats[$player_name]["faction"]);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("ac.on"));
        return true;
    }
}