<?php

namespace EschieEsh\factionsZ\tasks;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use EschieEsh\factionsZ\factionsZ;

class AllyTask extends PluginTask {
    /**
    * @return factionsZ|Plugin
    */
    private $plugin;
    /**
    * @return string
    */
    private $player = null;
    /**
    * @param factionsZ|Plugin
    * @param string $player
    */
    public function __construct(factionsZ $plugin, string $player) 
    {
        parent::__construct($plugin, $player);
        $this->plugin = $plugin;
        $this->player = $player;
    }
    /**
    * @return factionsZ|Plugin
    */
    public function getPlugin() 
    {
        return $this->plugin;
    }

    public function onRun($tick) 
    {
        if(isset($this->plugin->ally_requests[$this->player]))
        {
            if(($player = $this->plugin->getServer()->getPlayerExact($this->player)) instanceof Player)
            {
                $player->sendMessage($this->getPlugin()->getLanguage()->translateString("error.ally_timeout"));
            }
            unset($this->plugin->ally_requests[$this->player]);
        }

        $this->getPlugin()->getServer()->getScheduler()->cancelTask($this->getTaskId());
    }
}