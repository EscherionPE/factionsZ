<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class ADenySubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.adeny");
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
        if(!isset($this->getPlugin()->ally_requests[$player_name]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_requested_alliance"));
            return true;
        }
        $requestedby_name = $this->getPlugin()->ally_requests[$player_name]["requestedby"];
        $requested = $sender->getServer()->getPlayerExact($requestedby_name);
        
        if($requested instanceof Player)
        {
            $requested->sendMessage(TextFormat::RED . $this->translateString("adeny.denied", [$player_name, $faction_name]));
        }
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("adeny.success", [$requestedby_name]));
        unset($this->getPlugin()->ally_requests[$player_name]);
        return true;
        
    }
}