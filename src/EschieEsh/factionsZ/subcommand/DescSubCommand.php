<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class DescSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.desc");
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
        $desc = "";
        foreach($args as $word)
        {
            $desc .= ($word . " ");
        }
        $player_name = $sender->getName();
        $min_chars = $this->getPlugin()->getConfig()->get("desc_min_characters");
        $max_chars = $this->getPlugin()->getConfig()->get("desc_max_characters");
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        
        if ($this->getPlugin()->getRank($player_name) == "Member")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.lowrank"));
            return true;
        }
        if (strlen($desc) < $min_chars || strlen($desc) > $max_chars)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.desc_length", [$min_chars, $max_chars]));
            return true;
        }
        
        $this->getPlugin()->setDesc($faction_name, $desc);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("desc.success"));
        return true;
    }
}