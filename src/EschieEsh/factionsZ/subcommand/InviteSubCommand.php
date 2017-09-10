<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

class InviteSubCommand extends SubCommand
{
    /**
    * @param CommandSender $sender
    * @return bool
    */
    public function canUse(CommandSender $sender)
    {
        return $sender instanceof Player && $sender->hasPermission("factionsZ.command.invite");
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
        
        $player_name = $sender->getName();
        $invited_name = $args[0];
        if($player_name == $invited_name)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.yourself"));
            return true;
        }
        if (!$this->getPlugin()->isInFaction($player_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.not_in_fac"));
            return true;
        }
        $faction_name = $this->getPlugin()->getFaction($player_name);
        $max_players = $this->getPlugin()->getConfig()->get("max_players");
		if($this->getPlugin()->getFactionPlayersAmount($faction_name) == $max_players)
		{
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.faction_full"));
			return true;
		}
        $invited = $this->getPlugin()->getServer()->getPlayerExact($invited_name);
        if(!$invited instanceof Player)
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_offline", [$invited_name]));
            return true;
        }
        
        if($this->getPlugin()->getRank($player_name) == "Member")
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.low_rank"));
            return true;
        }
        if($this->getPlugin()->isInFaction($invited_name))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.player_already_in_fac",[$invited_name]));
            return true;
        }
        if(isset($this->getPlugin()->inv_requests[$invited_name]))
        {
            $sender->sendMessage(TextFormat::RED . $this->translateString("error.already_invited", [$invited_name]));
            return true;
        }
        $inv_timeout = $this->getPlugin()->getConfig()->get("inv_timeout");
        $this->getPlugin()->inv_requests[$invited_name] = ["faction" => $faction_name, "invitedby" => $player_name];
        $this->getPlugin()->invitePlayer($invited_name, $inv_timeout);
        $invited->sendMessage(TextFormat::GREEN . $this->translateString("invite.invited", [$faction_name, $player_name, $inv_timeout]));
        $sender->sendMessage(TextFormat::GREEN . $this->translateString("invite.success", [$invited_name, $faction_name]));
        
        return true;
    }
}