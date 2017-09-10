<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class ClaimSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.claim");
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
        
    
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.must_be_leader"));
            return true;
        }
        if($this->getPlugin()->hasClaim($faction_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_has_claim"));
            return true;
        }
        $coords = ["x" => $sender->getFloorX(), "z" => $sender->getFloorZ(), "world" => $sender->getLevel()->getName()];
        if (!$this->getPlugin()->canClaim($coords))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_claimed_nearby"));
            return true;
        }
     
        $this->getPlugin()->setClaim($faction_name, $coords);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("claim.success", [$faction_name]));
    
        return true;
    }
}