<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class SetHomeSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.sethome");
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
        if($this->getPlugin()->getRank($player_name) == "Member")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.low_rank"));
            return true;
        }
        $coords = ["x" => $sender->getFloorX(), 
                   "y" => $sender->getFloorY(), 
                   "z" => $sender->getFloorZ(), 
                   "world" => $sender->getLevel()->getName()];
        $this->getPlugin()->setHomeCoords($faction_name, $coords);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("sethome.success"));
        return true;
        
    }
}