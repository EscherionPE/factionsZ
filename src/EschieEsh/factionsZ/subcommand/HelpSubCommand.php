<?php
namespace EschieEsh\factionsZ\subcommand;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use EschieEsh\factionsZ\factionsZ;
use EschieEsh\factionsZ\Commands;
use pocketmine\command\CommandSender;

class HelpSubCommand extends SubCommand{
	/** @var  Commands */
	private $cmds;
	public function __construct(factionsZ $plugin, $name, $cmds){
		parent::__construct($plugin, $name);
		$this->cmds = $cmds;
	}
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public function canUse(CommandSender $sender){
		return $sender->hasPermission("factionsZ.command.help");
	}
	/**
	 * @param CommandSender $sender
	 * @param string[] $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args){
		if (empty($args))
        {
			$page = 1;
		} 
        else if (is_numeric($args[0]))
        {
			$page = (int) array_shift($args);
			if ($page <= 0)
            {
				$page = 1;
			}
		} 
        else
        {
			return false;
		}
		$commands = [];
		foreach ($this->cmds->getCommands() as $command)
        {
			if ($command->canUse($sender))
            {
				$commands[$command->getName()] = $command;
			}
		}
		ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
		$commands = array_chunk($commands, 6);
		$page = min(count($commands), $page);
		$sender->sendMessage($this->translateString("help.top", [$page, count($commands)]));
		foreach ($commands[$page - 1] as $command)
        {
			$sender->sendMessage($command->getUsage() . " - " . $command->getDescription());
		}
		return true;
	}
}