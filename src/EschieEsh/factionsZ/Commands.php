<?php
namespace EschieEsh\factionsZ;

use pocketmine\utils\TextFormat;
use EschieEsh\factionsZ\factionsZ;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use EschieEsh\factionsZ\subcommand\SubCommand;
use EschieEsh\factionsZ\subcommand\CreateSubCommand;
use EschieEsh\factionsZ\subcommand\DisbandSubCommand;
use EschieEsh\factionsZ\subcommand\InviteSubCommand;
use EschieEsh\factionsZ\subcommand\AcceptSubCommand;
use EschieEsh\factionsZ\subcommand\DenySubCommand;
use EschieEsh\factionsZ\subcommand\LeaveSubCommand;
use EschieEsh\factionsZ\subcommand\KickSubCommand;
use EschieEsh\factionsZ\subcommand\DescSubCommand;
use EschieEsh\factionsZ\subcommand\InfoSubCommand;
use EschieEsh\factionsZ\subcommand\PromoteSubCommand;
use EschieEsh\factionsZ\subcommand\DemoteSubCommand;
use EschieEsh\factionsZ\subcommand\HelpSubCommand;
use EschieEsh\factionsZ\subcommand\PLeadSubCommand;
use EschieEsh\factionsZ\subcommand\AboutSubCommand;
use EschieEsh\factionsZ\subcommand\SetHomeSubCommand;
use EschieEsh\factionsZ\subcommand\UnsetHomeSubCommand;
use EschieEsh\factionsZ\subcommand\HomeSubCommand;
use EschieEsh\factionsZ\subcommand\AllySubCommand;
use EschieEsh\factionsZ\subcommand\AAcceptSubCommand;
use EschieEsh\factionsZ\subcommand\ADenySubCommand;
use EschieEsh\factionsZ\subcommand\UnallySubCommand;
use EschieEsh\factionsZ\subcommand\ClaimSubCommand;
use EschieEsh\factionsZ\subcommand\UnclaimSubCommand;
use EschieEsh\factionsZ\subcommand\AdminSubCommand;
use EschieEsh\factionsZ\subcommand\ChatSubCommand;
use EschieEsh\factionsZ\subcommand\AChatSubCommand;
use EschieEsh\factionsZ\subcommand\TopSubCommand;

class Commands extends PluginCommand
{
	/** @var SubCommand[] */
    private $subCommands = [];
	/** @var SubCommand[] */
    private $aliasSubCommands = [];
	/** @var factionsZ */
    private $plugin;
    
    public function __construct(factionsZ $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin->getLanguage()->get("command.name"), $plugin);
        $this->setPermission("factionsZ.command");
        $this->setAliases([$plugin->getLanguage()->get("command.alias")]);
        $this->setDescription($plugin->getLanguage()->get("command.desc"));
        $this->setUsage($plugin->getLanguage()->get("command.usage"));
        $this->loadSubCommand(new CreateSubCommand($plugin, "create", $this));
        $this->loadSubCommand(new DisbandSubCommand($plugin, "disband", $this));
        $this->loadSubCommand(new InviteSubCommand($plugin, "invite", $this));
        $this->loadSubCommand(new AcceptSubCommand($plugin, "accept", $this));
        $this->loadSubCommand(new DenySubCommand($plugin, "deny", $this));
        $this->loadSubCommand(new LeaveSubCommand($plugin, "leave", $this));
        $this->loadSubCommand(new KickSubCommand($plugin, "kick", $this));
        $this->loadSubCommand(new DescSubCommand($plugin, "desc", $this));
        $this->loadSubCommand(new InfoSubCommand($plugin, "info", $this));
        $this->loadSubCommand(new PromoteSubCommand($plugin, "promote", $this));
        $this->loadSubCommand(new DemoteSubCommand($plugin, "demote", $this));
        $this->loadSubCommand(new HelpSubCommand($plugin, "help", $this));
        $this->loadSubCommand(new PLeadSubCommand($plugin, "plead", $this));
        $this->loadSubCommand(new AboutSubCommand($plugin, "about", $this));
        $this->loadSubCommand(new SetHomeSubCommand($plugin, "sethome", $this));
        $this->loadSubCommand(new HomeSubCommand($plugin, "home", $this));
        $this->loadSubCommand(new UnsetHomeSubCommand($plugin, "unsethome", $this));
        $this->loadSubCommand(new AllySubCommand($plugin, "ally", $this));
        $this->loadSubCommand(new AAcceptSubCommand($plugin, "aaccept", $this));
        $this->loadSubCommand(new ADenySubCommand($plugin, "adeny", $this));
        $this->loadSubCommand(new UnallySubCommand($plugin, "unally", $this));
        $this->loadSubCommand(new ClaimSubCommand($plugin, "claim", $this));
        $this->loadSubCommand(new UnclaimSubCommand($plugin, "unclaim", $this));
        $this->loadSubCommand(new AdminSubCommand($plugin, "admin", $this));
        $this->loadSubCommand(new ChatSubCommand($plugin, "c", $this));
        $this->loadSubCommand(new AChatSubCommand($plugin, "ac", $this));
        $this->loadSubCommand(new TopSubCommand($plugin, "top", $this));
        $this->plugin->getLogger()->debug("all faction commands have been registered!");
    }
	/**
	 * @return SubCommand[]
	 */
    public function getCommands(): array
    {
        return $this->subCommands;
    }
	/**
	 * @param SubCommand $command
	 */
    private function loadSubCommand(SubCommand $command)
    {
        $this->subCommands[$command->getName()] = $command;
        if ($command->getAlias() != "")
        {
            $this->aliasSubCommands[$command->getAlias()] = $command;
        }
    }
	/**
	 * @param CommandSender $sender
	 * @param string[] $args
	 * @return bool
	 */
    public function execute(CommandSender $sender, $alias, array $args)
	{
        if (!isset($args[0]))
        {
            $sender->sendMessage($this->plugin->getLanguage()->translateString("command.redirect"));
            return false;
        }
        $subCommand = strtolower(array_shift($args));
        if (isset($this->subCommands[$subCommand]))
        {
            $command = $this->subCommands[$subCommand];
        } 
        elseif (isset($this->aliasSubCommands[$subCommand]))
        {
            $command = $this->aliasSubCommands[$subCommand];
        } 
        else
        {
            $sender->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->get("command.unknown"));
            return true;
        }
        
        if ($command->canUse($sender))
        {
            if (!$command->execute($sender, $args))
            {
                $usage = $this->plugin->getLanguage()->translateString("subcommand.usage", [$command->getUsage()]);
                $sender->sendMessage($usage);
            }
        } 
        else
        {
            $sender->sendMessage(TextFormat::RED . $this->plugin->getLanguage()->get("command.unknown"));
        }
        return true;
    }
}