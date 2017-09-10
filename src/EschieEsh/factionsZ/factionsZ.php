<?php

namespace EschieEsh\factionsZ;
    
use pocketmine\plugin\PluginBase;
use pocketmine\Command\Command;
use pocketmine\Command\CommandSender;
use pocketmine\lang\BaseLang;
use pocketmine\utils\TextFormat;
use EschieEsh\factionsZ\dataprovider\DataProvider;
use EschieEsh\factionsZ\dataprovider\SQLiteDataProvider;
class factionsZ extends PluginBase
{
    /** @var DataProvider $dataProvider */
    private $dataProvider = null;
    
    /** @var BaseLang $baseLang */
    private $baseLang = null;
    
    //// Storing temporary data in here ///
    public $claims = [];
    public $is_admin = [];
    public $inv_requests = [];
    public $ally_requests = [];
    public $faction_chats = [];
    ///////////////////////////////////////
    
    /**
    * @api
    * @return BaseLang
    */
    public function getLanguage(): BaseLang
    {
        return $this->baseLang;
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function createFaction(string $player, string $faction)
    {
        $this->dataProvider->createFaction($player, $faction);
    }
    /**
    * @param string $faction
    */
    public function disbandFaction(string $faction)
    {
        $this->dataProvider->disbandFaction($faction);
    }
    /**
    * @param string $player
    * @param int $inv_timeout
    */
    public function invitePlayer(string $player, int $inv_timeout)
    {
        $this->dataProvider->invitePlayer($player, $inv_timeout);
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function joinFaction(string $player, string $faction)
    {
        $this->dataProvider->joinFaction($player, $faction);
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function kickPlayer(string $player, string $faction)
    {
        $this->dataProvider->kickPlayer($player, $faction);
    }
    /**
    * @param string $faction
    * @param string $rank
    * @return []
    */
    public function getPlayersByRank(string $faction, string $rank, bool $state) : array
    {
        return $this->dataProvider->getPlayersByRank($faction, $rank, $state);
    }
    /**
    * @param string $faction
    * @return string
    */
    public function getDesc(string $faction) : string
    {
        return $this->dataProvider->getDesc($faction);
    }
    /**
    * @param string $player
    * @param string $description
    */
    public function setDesc(string $faction, string $description)
    {
        $this->dataProvider->setDesc($faction, $description);
    }
    /**
    * @param string $player
    * @return bool
    */
    public function isInFaction(string $player) : bool
    {
        return $this->dataProvider->isInFaction($player);
    }
    /**
    * @param string $player
    * @return string
    */
    public function getFaction(string $player) : string
    {
        return $this->dataProvider->getFaction($player);
    }
   
    /**
    * @param string $faction
    * @return []
    */
    public function getFactionInfo(string $faction) : array
    {
        return $this->dataProvider->getFactionInfo($faction);
    }
    /**
    * @param string $faction
    * @return bool
    */
    public function factionExists(string $faction) : bool
    {
        return $this->dataProvider->factionExists($faction);
    }
    /**
    * @param string $player
    * @return string
    */
    public function getRank(string $player) : string
    {
        return $this->dataProvider->getRank($player);
    }
    /**
    * @param string $faction
    * @return string
    */
    public function getLeader(string $faction) : string
    {
        return $this->dataProvider->getLeader($faction);
    }
    /**
    * @param string $player
    * @param string $faction
    * @param string $passedto
    */
    public function passLeadership(string $player, string $faction, string $passedto)
    {
        $this->dataProvider->passLeadership($player, $faction, $passedto);
    }
    /**
    * @param string $player
    * @param string $rank
    */
    public function setRank(string $player, string $rank)
    {
        $this->dataProvider->setRank($player, $rank);
    }
    /**
    * @param string $faction
    * @param array $coords
    */
    public function setHomeCoords(string $faction, array $coords)
    {
        $this->dataProvider->setHomeCoords($faction, $coords);
    }
    /**
    * @param string $faction
    * @return bool
    */
    public function hasHome(string $faction) : bool
    {
        return $this->dataProvider->hasHome($faction);
    }
    /**
    * @param string $faction
    * @return []
    */
    public function getHomeCoords(string $faction) : array
    {
        return $this->dataProvider->getHomeCoords($faction);
    }
    /**
    * @param string $faction
    */
    public function unsetHome(string $faction)
    {
        $this->dataProvider->unsetHome($faction);
    }
    /**
    * @param string $faction1
    * @param string $faction2
    * @param bool $state
    */
    public function setAllies(string $faction1, string $faction2, bool $state)
    {
        $this->dataProvider->setAllies($faction1, $faction2, $state);
    }
    /**
    * @param string $faction
    * @return []
    */
    public function getAllies(string $faction) : array
    {
        return $this->dataProvider->getAllies($faction);
    }
    /**
    * @param string $faction1
    * @param string $faction2
    * @return bool
    */
    public function areAllies(string $faction1, string $faction2) : bool
    {
        return $this->dataProvider->areAllies($faction1, $faction2);
    }
    /**
    * @param string $faction
    * @return int
    */
    public function countAllies(string $faction)  : int
    {
        return $this->dataProvider->countAllies($faction);
    }
    /**
    * @param string $player
    * @param int $ally_timeout
    */
    public function requestAlliance(string $player, int $ally_timeout)
    {
        $this->dataProvider->requestAlliance($player, $ally_timeout);
    }
    /**
    * @param string $faction
    * @param array $coords
    */
    public function setClaim(string $faction, array $coords)
    {
       $this->dataProvider->setClaim($faction, $coords);
    }
    /**
    * @param array $coords
    * @return bool
    */
    public function canClaim(array $coords) : bool
    {
        return $this->dataProvider->canClaim($coords);
    }
    /**
    * @param string $faction
    * @return bool
    */
    public function hasClaim(string $faction) : bool
    {
        return $this->dataProvider->hasClaim($faction);
    }
	/**
    * @param string $faction
    * @return int
    */
	public function getFactionPlayersAmount(string $faction)
	{
		return $this->dataProvider->getFactionPlayersAmount($faction);	
	}
	/**
    * @param int $length
    * @return array
    */
    public function getTopFactions(int $length) : array
    {
        return $this->dataProvider->getTopFactions($length);
    }
    /**
    * @param array $coords
    * @return string|null
    */
    public function getFactionByClaim(array $coords)
    {
        return $this->dataProvider->getFactionByClaim($coords);
    }
    /**
    * @param string $faction
    * @param int $strength
    */
    public function setStrength(string $faction, int $strength)
    {
        $this->dataProvider->setStrength($faction, $strength);
    }
    
    /**
    * @param string $faction
    * @return int
    */
    public function getStrength(string $faction) : int
    {
        return $this->dataProvider->getStrength($faction);
    }
    /**
    * @api
    * @return DataProvider
    */
    public function getProvider(): DataProvider
    {
        return $this->dataProvider;
    }
    // instance for plugins
    public function getInstance()
    {
        return $this;
    }
    //////////////////////////
    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->getLogger()->notice("Loading...");
        $this->saveDefaultConfig();
        $this->reloadConfig();
		
        $lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
        $this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");

        switch (strtolower($this->getConfig()->get("DataProvider")))
        {
            case "sqlite":
            case "sqlite3":  
            default:
                $this->dataProvider = new SQLiteDataProvider($this); /// sqlite 
                break;
        }
		
		$eventListener = new EventListener($this);
		$this->getServer()->getPluginManager()->registerEvents($eventListener, $this);
		
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
        $this->getLogger()->notice(TextFormat::GREEN . "Enabled!");
    }
    
    public function onDisable()
    {
        if ($this->dataProvider !== null)
        {
            $this->dataProvider->close();
        }
    }
}