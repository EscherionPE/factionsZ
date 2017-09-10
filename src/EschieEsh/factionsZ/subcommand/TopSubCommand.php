<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class TopSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender->hasPermission("factionsZ.command.top");
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
       
		$length = $this->getPlugin()->getConfig()->get("top_length");
		
        $top_factions = $this->getPlugin()->getTopFactions($length);
		
		$sender->sendMessage($this->translateString("top.header", [$length]));
		
		foreach($top_factions as $key => $faction)
		{
			$sender->sendMessage($this->translateString("top.faction", [$key+1, $faction[0], $faction[1]]));
		}
    
        return true;
    }
}