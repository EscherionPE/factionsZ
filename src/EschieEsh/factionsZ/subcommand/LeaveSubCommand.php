<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class LeaveSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.leave");
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
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if($this->getPlugin()->getRank($player_name) == "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.has_leadership"));
            return true;
        }
        
        
        $this->getPlugin()->kickPlayer($player_name, $faction_name);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("leave.success", [$faction_name]));
        return true;
    }
}