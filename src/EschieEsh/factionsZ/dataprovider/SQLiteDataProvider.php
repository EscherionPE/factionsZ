<?php
namespace EschieEsh\factionsZ\dataprovider;

use pocketmine\Player;
use EschieEsh\factionsZ\factionsZ;
use pocketmine\command\CommandSender;
use EschieEsh\factionsZ\tasks\AllyTask;
use EschieEsh\factionsZ\tasks\InviteTask;

class SQLiteDataProvider extends DataProvider
{
    /** @var \SQLite3 */
    private $db;
    /** @var \SQLite3Stmt */
    private $sqlCreateFaction_PlayerData, $sqlCreateFaction_FactionData, $sqlGetPlayerFaction, $sqlFactionExists, $sqlDeleteFaction_PlayerData, $sqlDeleteFaction_FactionData, $sqlKickPlayer, $sqlInvitePlayer, $sqlGetDescription, $sqlDeleteInvitation, $sqlGetFactionInfo, $sqlGetPlayersByRank, $sqlGetPlayerRank, $sqlSetPlayerRank, $sqlPlayerInFaction, $sqlGetLeader, $sqlGetFactionPlayerAmount, $sqlSetFactionPlayerAmount, $sqlGetHome, $sqlSetHome, $sqlHasHome, $sqlUnsetHome, $sqlCountAllies, $sqlSetAllies, $sqlUnsetAllies, $sqlAreAllies, $sqlGetAllies, $sqlSetStrength, $sqlGetStrength, $sqlAllyFaction, $sqlClaim, $sqlPointInClaim, $sqlClaimExists, $sqlUnclaim, $sqlSelectTopFactions;
    /**
    * @param factionsZ $plugin
    */
    public function __construct(factionsZ $plugin)
    {
        parent::__construct($plugin);
        $this->db = new \SQLite3($this->plugin->getDataFolder() . "factionsZ.db");
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS players
            (player TEXT PRIMARY KEY COLLATE NOCASE, faction TEXT, rank TEXT);"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS factions
            (faction TEXT PRIMARY KEY, leader TEXT, amount_players INT, description TEXT, strength INT);"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS homes
            (faction TEXT PRIMARY KEY, homeX INT, homeY INT, homeZ INT, homeWorld TEXT);"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS allies
            (id INT PRIMARY KEY, faction1 TEXT, faction2 TEXT);"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS claims
            (faction TEXT PRIMARY KEY, claimX INT, claimX1 INT, claimZ INT, claimZ1 INT, claimWorld TEXT);"
        );
        $this->sqlGetStrength = $this->db->prepare(
            "SELECT strength FROM factions WHERE faction = :faction;"
        );
        $this->sqlSetStrength = $this->db->prepare(
            "UPDATE factions SET strength = :strength WHERE faction = :faction;"
        );
        ///
        $this->sqlClaim = $this->db->prepare(
            "INSERT OR REPLACE INTO claims (faction, claimX, claimX1, claimZ, claimZ1, claimWorld) VALUES 
            (:faction, :claimX, :claimX1, :claimZ, :claimZ1, :claimWorld);"
        );
        $this->sqlPointInClaim = $this->db->prepare(
            "SELECT faction FROM claims WHERE 
            :x >= claimX AND :x <= claimX1 AND :z >= claimZ AND :z <= claimZ1 AND claimWorld = :world;"
        );
        $this->sqlClaimExists = $this->db->prepare(
            "SELECT faction FROM claims WHERE faction = :faction;"
        );
        $this->sqlUnclaim = $this->db->prepare(
            "DELETE FROM claims WHERE faction = :faction;"
        );
        /////////////
        $this->sqlCreateFaction_PlayerData = $this->db->prepare(
            "INSERT OR REPLACE INTO players (player, faction, rank) VALUES 
            (:player, :faction, :rank);"
        );
        $this->sqlCreateFaction_FactionData = $this->db->prepare(
            "INSERT OR REPLACE INTO factions (faction, leader, amount_players, description, strength) VALUES
            (:faction, :leader, :amount_players, :description, :strength);"
        );
       
        $this->sqlFactionExists = $this->db->prepare(
            "SELECT faction FROM factions WHERE faction = :faction;"
        );
        $this->sqlGetPlayerFaction = $this->db->prepare(
            "SELECT faction FROM players WHERE player = :player;"
        );
        $this->sqlDeleteFaction_FactionData = $this->db->prepare(
            "DELETE FROM factions WHERE faction = :faction;"
        );
        $this->sqlDeleteFaction_PlayerData = $this->db->prepare(
            "DELETE FROM players WHERE faction = :faction;"
        );
        $this->sqlKickPlayer = $this->db->prepare(
            "DELETE FROM players WHERE player = :player AND faction = :faction;"
        );
        $this->sqlGetDescription = $this->db->prepare(
            "SELECT description FROM factions WHERE faction = :faction;"
        );
        $this->sqlSetDescription = $this->db->prepare(
            "UPDATE factions SET description = :description WHERE faction = :faction;"
        );
        $this->sqlGetFactionInfo = $this->db->prepare(
            "SELECT * FROM factions WHERE faction = :faction;"
        );
        $this->sqlGetPlayersByRank = $this->db->prepare(
            "SELECT player FROM players WHERE faction = :faction AND rank = :rank;"
        );
        $this->sqlPlayerInFaction = $this->db->prepare(
            "SELECT player FROM players WHERE player = :player;"
        );
        $this->sqlGetPlayerRank = $this->db->prepare(
            "SELECT rank FROM players WHERE player = :player;"
        );
        $this->sqlSetPlayerRank = $this->db->prepare(
            "UPDATE players SET rank = :rank WHERE player = :player;"
        );
        $this->sqlGetLeader = $this->db->prepare(
            "SELECT leader FROM factions WHERE faction = :faction;"
        );
        $this->sqlSetLeader = $this->db->prepare(
            "UPDATE factions SET leader = :leader WHERE faction = :faction;"
        );
        $this->sqlGetFactionPlayerAmount = $this->db->prepare(
            "SELECT amount_players FROM factions WHERE faction = :faction;"
        );
        $this->sqlSetFactionPlayerAmount = $this->db->prepare(
            "UPDATE factions SET amount_players = :amount_players WHERE faction = :faction;"
        );
        $this->sqlSetHome = $this->db->prepare(
            "INSERT OR REPLACE INTO homes (faction, homeX, homeY, homeZ, homeWorld) VALUES 
            (:faction, :homeX, :homeY, :homeZ, :homeWorld);"
        );
        $this->sqlHasHome = $this->db->prepare(
            "SELECT faction FROM homes WHERE faction = :faction;"
        );
        $this->sqlGetHome = $this->db->prepare(
            "SELECT * FROM homes WHERE faction = :faction;"
        );
        $this->sqlUnsetHome = $this->db->prepare(
            "DELETE FROM homes WHERE faction = :faction;"
        );
        $this->sqlCountAllies = $this->db->prepare(
            "SELECT COUNT(faction2) AS cnt FROM allies WHERE faction1 = :faction1;"
        );
        $this->sqlAreAllies = $this->db->prepare(
            "SELECT id FROM allies WHERE (faction1 = :faction1 AND faction2 = :faction2) OR (faction1 = :faction2 AND faction2 = :faction1);"
        );
        $this->sqlGetAllies = $this->db->prepare(
            "SELECT faction2 FROM allies WHERE faction1 = :faction1;"
        );
        $this->sqlSetAllies = $this->db->prepare(
            "INSERT INTO allies (faction1, faction2) VALUES (:faction1, :faction2);"
        );
        
        $this->sqlUnsetAllies = $this->db->prepare(
            "DELETE FROM allies WHERE faction1 = :faction1 AND faction2 = :faction2;"
        );
        
        $this->sqlDeleteAllies = $this->db->prepare(
            "DELETE FROM allies WHERE faction1 = :faction OR faction2 = :faction;"
        );
		$this->sqlSelectTopFactions = $this->db->prepare(
            "SELECT faction, strength FROM factions ORDER BY strength DESC LIMIT :length;"
        );
        $this->plugin->getLogger()->debug("SQLite data provider registered");
    }
	/**
    * @param int $length
    * @return [];
    */
    public function getTopFactions(int $length) : array
	{
		$factions = [];
		$this->sqlSelectTopFactions->bindValue(":length", $length);
		$this->sqlSelectTopFactions->reset();
		$result = $this->sqlSelectTopFactions->execute();
		while($resultArr = $result->fetchArray(SQLITE3_ASSOC))
		{
			array_push($factions, [$resultArr['faction'], $resultArr['strength']]);
		}
		return $factions;
		
	}
    /**
    * @param string $faction
    * @param int $strength
    */
    public function setStrength(string $faction, int $strength)
    {
        if($strength < 0)
        {
            $strength = 0;
        }
        $this->sqlSetStrength->bindValue(":faction", $faction);
        $this->sqlSetStrength->bindValue(":strength", $strength);
        $this->sqlSetStrength->reset();
        $this->sqlSetStrength->execute();
    }
    
    /**
    * @param string $faction
    * @return int
    */
    public function getStrength(string $faction) : int
    {
        $this->sqlGetStrength->bindValue(":faction", $faction);
        $this->sqlGetStrength->reset();
        $result = $this->sqlGetStrength->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArr["strength"];
    }
    /**
    * @param string $faction
    * @param array $coords
    */
    public function setClaim(string $faction, array $coords)
    {
        if(empty($coords))
        {
            $this->sqlUnclaim->bindValue(":faction", $faction);
            $this->sqlUnclaim->reset();
            $this->sqlUnclaim->execute();
            return;
        }
        
        $size = $this->plugin->getConfig()->get("max_claim_size");
        $arm = ceil(($size-1)/2);
        $this->sqlClaim->bindValue(":faction", $faction);
        $this->sqlClaim->bindValue(":claimX", $coords["x"]-$arm);
        $this->sqlClaim->bindValue(":claimX1", $coords["x"]+$arm);
        $this->sqlClaim->bindValue(":claimZ", $coords["z"]-$arm);
        $this->sqlClaim->bindValue(":claimZ1", $coords["z"]+$arm);
        $this->sqlClaim->bindValue(":claimWorld", $coords["world"]);
        $this->sqlClaim->reset();
        $this->sqlClaim->execute();
    }
    /**
    * @param array $coords
    * @return bool
    */
    public function canClaim(array $coords) : bool
    {
        $size = $this->plugin->getConfig()->get("max_claim_size");
            
        $arm = ceil(($size-1)/2);
        $x = $coords["x"] + $arm;
        $x1 = $coords["x"] - $arm;
        $z = $coords["z"] + $arm;
        $z1 = $coords["z"] - $arm;
        $world = $coords["world"];
        $points = [[$x, $z], [$x, $z1], [$x1, $z], [$x1, $z1]];
        foreach($points as $point)
        {
            if($this->getFactionByClaim(["x" => $point[0], "z" => $point[1], "world" => $world]) != null)
            {
                return false;
            }
        }
        return true;
    }
    /**
    * @param string $faction
    * @return bool
    */
    public function hasClaim(string $faction) : bool
    {
        $this->sqlClaimExists->bindValue(":faction", $faction);
        $this->sqlClaimExists->reset();
        $result = $this->sqlClaimExists->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return !empty($resultArr);
    }
    /**
    * @param array $coords
    * @return string|null
    */
    public function getFactionByClaim(array $coords)
    {
        $this->sqlPointInClaim->bindValue(":x", $coords["x"]);
        $this->sqlPointInClaim->bindValue(":z", $coords["z"]);
        $this->sqlPointInClaim->bindValue(":world", $coords["world"]);
        $this->sqlPointInClaim->reset();
        $result = $this->sqlPointInClaim->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        if(empty($resultArr))
        {
            return null;
        }
        return $resultArr["faction"];
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function createFaction(string $player, string $faction)
    {
        $this->create_playerData($player, $faction, "Leader");
        $stmt = $this->sqlCreateFaction_FactionData;
        $stmt->bindValue(":faction", $faction);
        $stmt->bindValue(":leader", $player);
        $stmt->bindValue(":amount_players", 1);
        $stmt->bindValue(":description", "Description is not set");
        $stmt->bindValue(":strength", 0);
        $stmt->reset();
        $stmt->execute(); 
    }
   
    /**
    * @param string $faction
    * @return string
    */
    public function getLeader(string $faction) : string
    {
        $this->sqlGetLeader->bindValue(":faction", $faction);
        $this->sqlGetLeader->reset();
        $result = $this->sqlGetLeader->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArr["leader"];
    }
    /**
    * @param string $faction
    * @return int
    */
    public function getFactionPlayersAmount(string $faction) : int
    {
        $this->sqlGetFactionPlayerAmount->bindValue(":faction", $faction);
        $this->sqlGetFactionPlayerAmount->reset();
        $result = $this->sqlGetFactionPlayerAmount->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArr["amount_players"];
    }
    /**
    * @param string $faction
    * @param int $amount
    */
    public function setFactionPlayersAmount(string $faction, int $amount)
    {
        $this->sqlSetFactionPlayerAmount->bindValue(":faction", $faction);
        $this->sqlSetFactionPlayerAmount->bindValue(":amount_players", $amount);
        $this->sqlSetFactionPlayerAmount->reset();
        $this->sqlSetFactionPlayerAmount->execute();
    }            
    /**
    * @param string $player
    * @return bool
    */
    public function isInFaction(string $player) : bool
    {
        $this->sqlPlayerInFaction->bindValue(":player", $player);
        $this->sqlPlayerInFaction->reset();
        $result = $this->sqlPlayerInFaction->execute(); 
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return !empty($resultArr);
    }
    /**
    * @param string $player
    * @param int $inv_timeout
    */
    public function invitePlayer(string $player, int $inv_timeout)
    {
        $task = new InviteTask($this->plugin, $player);
        $this->plugin->getServer()->getScheduler()->scheduleDelayedTask($task, $inv_timeout * 20);
    }
     /**
    * @param string $player
    * @param int $ally_timeout
    */
    public function requestAlliance(string $player, int $ally_timeout)
    {
        $task = new AllyTask($this->plugin, $player);
        $this->plugin->getServer()->getScheduler()->scheduleDelayedTask($task, $ally_timeout * 20);
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function joinFaction(string $player, string $faction)
    {
        $this->create_playerData($player, $faction, "Member");
        $this->setFactionPlayersAmount($faction, $this->getFactionPlayersAmount($faction) + 1);
    }
    /**
    * @param string $player
    * @param string $faction
    */
    public function kickPlayer(string $player, string $faction)
    {
        $this->sqlKickPlayer->bindValue(":player", $player);
        $this->sqlKickPlayer->bindValue(":faction", $faction);
        $this->sqlKickPlayer->reset();
        $this->sqlKickPlayer->execute();
        $this->setFactionPlayersAmount($faction, $this->getFactionPlayersAmount($faction) - 1);
		
		$this->unset_temporary_data($player);
    }
    /**
    * @param string $faction
    */
    public function disbandFaction(string $faction)
    {
        $stmts = [$this->sqlDeleteFaction_PlayerData, $this->sqlDeleteFaction_FactionData, 
                  $this->sqlUnsetHome, $this->sqlDeleteAllies, $this->sqlUnclaim];
        foreach ($stmts as $stmt)
        {
            $stmt->bindValue(":faction", $faction);
            $stmt->reset();
            $stmt->execute();
        }
    }
    /**
    * @param string $faction
    * @return string
    */
    public function getDesc(string $faction) : string
    {
        $this->sqlGetDescription->bindValue(":faction", $faction);
        $this->sqlGetDescription->reset();
        $result = $this->sqlGetDescription->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return $resultArr["description"];
        
    }
    /**
    * @param string $faction
    * @param string $description
    */
    public function setDesc(string $faction, string $description)
    {
        $this->sqlSetDescription->bindValue(":faction", $faction);
        $this->sqlSetDescription->bindValue(":description", $description);
        $this->sqlSetDescription->reset();
        $this->sqlSetDescription->execute();
    }
    /**
    * @param string $player
    * @return []
    */
    public function getFaction(string $player) : string
    {
        $this->sqlGetPlayerFaction->bindValue(":player", $player);
        $this->sqlGetPlayerFaction->reset();
        $result = $this->sqlGetPlayerFaction->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArr["faction"];
    }  
    /**
    * @param string $faction
    * return []
    */
    public function getFactionInfo(string $faction) : array
    {
        $this->sqlGetFactionInfo->bindValue(":faction", $faction);
        $this->sqlGetFactionInfo->reset();
        $result = $this->sqlGetFactionInfo->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        $faction_leader = $this->getLeader($faction);
        $faction_members = $this->getPlayersByRank($faction, "Member", true);
        $faction_officers = $this->getPlayersByRank($faction, "Officer", true);
        $faction_total = $this->getFactionPlayersAmount($faction);
        $faction_description = $resultArr["description"];
        $faction_strength = $resultArr["strength"];
        $faction_allies = [];
        if($this->countAllies($faction) > 0)
        {
            $faction_allies = $this->getAllies($faction);
        }
        return["leader" => $faction_leader,
              "officers" => $faction_officers,
              "members" => $faction_members,
              "description" => $faction_description,
              "strength" => $faction_strength,
              "amount_players" => $faction_total,
              "allies" => $faction_allies];
    }
    /**
    * @param string $faction
    */
    public function factionExists(string $faction) : bool
    {
        $this->sqlFactionExists->bindValue(":faction", $faction);
        $this->sqlFactionExists->reset();
        $result = $this->sqlFactionExists->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return empty($resultArr) == false;
    } 
    /**
    * @param string $faction
    * @param string $rank
    * @param bool $state
    * @return []
    */
    public function getPlayersByRank(string $faction, string $rank, bool $state) : array 
    {
        $players = [];
        $this->sqlGetPlayersByRank->bindValue(":faction", $faction);
        $this->sqlGetPlayersByRank->bindValue(":rank", $rank);
        $this->sqlGetPlayersByRank->reset();
        $result = $this->sqlGetPlayersByRank->execute();
        while($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $player_name = $row["player"];
            if(!$state || ($player = $this->plugin->getServer()->getPlayerExact($player_name)) instanceof Player)
            {
                array_push($players, (string) $player->getName());
            }
        }
        return $players;
    }
    /**
    * @param string $player
    * @return string
    */
    public function getRank(string $player) : string
    {
        $this->sqlGetPlayerRank->bindValue(":player", $player);
        $this->sqlGetPlayerRank->reset();
        $result = $this->sqlGetPlayerRank->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        return $resultArr["rank"];
    }
    /**
    * @param string $player
    * @param string $faction
    * @param string $passedto
    */
    public function passLeadership(string $player, string $faction, string $passedto)
    {
        $this->setRank($player, "Officer");
        $this->setRank($passedto, "Leader");
        $this->sqlSetLeader->bindValue(":faction", $faction);
        $this->sqlSetLeader->bindValue(":leader", $leader);
        $this->sqlSetLeader->reset();
        $this->sqlSetLeader->execute();
    }
    /**
    * @param string $player
    * @param string $rank
    */
    public function setRank(string $player, string $rank)
    {
        $this->sqlSetPlayerRank->bindValue(":player", $player);
        $this->sqlSetPlayerRank->bindValue(":rank", $rank);
        $this->sqlSetPlayerRank->reset();
        $this->sqlSetPlayerRank->execute();
    }
    /**
    * @param string $faction
    * @param array $coords
    */
    public function setHomeCoords(string $faction, array $coords)
    {
        $this->sqlSetHome->bindValue(":faction", $faction);
        $this->sqlSetHome->bindValue(":homeX", $coords["x"]);
        $this->sqlSetHome->bindValue(":homeY", $coords["y"]);
        $this->sqlSetHome->bindValue(":homeZ", $coords["z"]);
        $this->sqlSetHome->bindValue(":homeWorld", $coords["world"]);
        $this->sqlSetHome->reset();
        $this->sqlSetHome->execute();
    }
    /**
    * @param string $faction
    * @return bool
    */
    public function hasHome(string $faction) : bool
    {
        $this->sqlHasHome->bindValue(":faction", $faction);
        $this->sqlHasHome->reset();
        $result = $this->sqlHasHome->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return !empty($resultArr);
    }
    /**
    * @param string $faction
    */
    public function unsetHome(string $faction)
    {
        $this->sqlUnsetHome->bindValue(":faction", $faction);
        $this->sqlUnsetHome->reset();
        $this->sqlUnsetHome->execute();
    }
    /**
    * @param string $faction
    * @return []
    */
    public function getHomeCoords(string $faction) : array
    {
        $this->sqlGetHome->bindValue(":faction", $faction);
        $this->sqlGetHome->reset();
        $result = $this->sqlGetHome->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return $resultArr;
    }
    /**
    * @param string $faction
    * @return int
    */
    public function countAllies(string $faction) : int
    {
        $this->sqlCountAllies->bindValue(":faction1", $faction);
        $this->sqlCountAllies->reset();
        $result = $this->sqlCountAllies->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return $resultArr["cnt"];
    }
    /**
    * @param string $faction1
    * @param string $faction2
    * @param string $state
    */
    public function setAllies(string $faction1, string $faction2, bool $state) 
    {
        if ($state)
        {
            $stmt = $this->sqlSetAllies; 
        } 
        else 
        {
            $stmt = $this->sqlUnsetAllies;    

        }
        $stmt->bindValue(":faction1", $faction1);
        $stmt->bindValue(":faction2", $faction2);
        $stmt->reset();
        $stmt->execute();
        
    }
    /**
    * @param string $faction1
    * @param string $faction2
    * @return bool
    */
    public function areAllies(string $faction1, string $faction2) : bool
    {
        $this->sqlAreAllies->bindValue(":faction1", $faction1);
        $this->sqlAreAllies->bindValue(":faction2", $faction2);
        $this->sqlAreAllies->reset();
        $result = $this->sqlAreAllies->execute();
        $resultArr = $result->fetchArray(SQLITE3_ASSOC);
        
        return !empty($resultArr);
    }
     /**
    * @param string $faction
    * @return []
    */
    public function getAllies(string $faction) : array
    {
        $allies = [];
        $this->sqlGetAllies->bindValue(":faction1", $faction);
        $this->sqlGetAllies->reset();
        $result = $this->sqlGetAllies->execute();
        while ($resultArr = $result->fetchArray(SQLITE3_ASSOC))
        {
            array_push($allies, (string) $resultArr["faction2"]);
        }
        
        return $allies;
    }
    /**
    * @param string $player
    * @param string $faction
    * @param string $rank
    */
    public function create_playerData(string $player, string $faction, string $rank)
    {
        $stmt = $this->sqlCreateFaction_PlayerData;
        $stmt->bindValue(":player", $player);
        $stmt->bindValue(":faction", $faction);
        $stmt->bindValue(":rank", $rank);
        $stmt->reset();
        $stmt->execute(); 
    }
    
    public function close()
    {
        $this->db->close();
        $this->plugin->getLogger()->debug("SQLite database closed!");
    }
}
