<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class CreateSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.create");
    }
    /**
    * @param CommandSender $sender
    * @param string[] $args
    * @return bool
    */
    public function execute(CommandSender $sender, array $args)
    {
        if (empty($args))
        {
            return false;
        }
        
        $faction_name = $args[0];
        $player_name = $sender->getName();
        $min_chars = $this->getPlugin()->getConfig()->get("name_min_characters");
        $max_chars = $this->getPlugin()->getConfig()->get("name_max_characters");
        if (!(ctype_alnum($faction_name))) 
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_alphanum"));
            return true;
        }
        if(strlen($faction_name) < $min_chars || strlen($faction_name) > $max_chars)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.name_length", [$min_chars, $max_chars]));
            return true;
        }
        
        if ($this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_in_fac", [$faction_name]));
            return true;
        }
        
        if ($this->getPlugin()->factionExists($faction_name))
        {
            
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.fac_alrdy_exsts", [$faction_name]));
            return true;
        }
        
        
        $this->getPlugin()->createFaction($player_name, $faction_name);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("create.success", [$faction_name]));
        return true;
    }
}