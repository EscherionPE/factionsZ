<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class UnallySubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.unally");
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
        $player_name = $sender->getName();

        if(!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        if ($faction_name == $args[0])
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.your_own_fac"));
            return true;
        }
        if($this->getPlugin()->getRank($player_name) != "Leader")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.low_rank"));
            return true;
        }
        if(!$this->getPlugin()->factionExists($args[0]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.fac_doesnt_exst", [$args[0]]));
            return true;
        }
        
        if(!$this->getPlugin()->areAllies($faction_name, $args[0]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.factions_arent_allied", [$args[0]]));
            return true;
        }
        $this->getPlugin()->setAllies($faction_name, $args[0], false);
        $this->getPlugin()->setAllies($args[0], $faction_name, false);
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("unally.success", [$args[0]]));
        return true;
        
    }
}