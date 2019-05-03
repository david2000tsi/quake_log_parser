<?php

class Parser
{
	// Constants.
	CONST LOG_FILE_NAME = __DIR__."/../games.log";
	CONST LOG_KEY_INIT_GAME = "InitGame";
	CONST LOG_KEY_SHUTDOWN_GAME = "ShutdownGame";
	CONST LOG_KEY_KILL = "Kill";
	CONST LOG_KEY_PLAYER = "ClientUserinfoChanged";
	CONST LOG_KEY_PLAYER_WORLD = "<world>";

	CONST KILL_MODES = array(
		'MOD_UNKNOWN',
		'MOD_SHOTGUN',
		'MOD_GAUNTLET',
		'MOD_MACHINEGUN',
		'MOD_GRENADE',
		'MOD_GRENADE_SPLASH',
		'MOD_ROCKET',
		'MOD_ROCKET_SPLASH',
		'MOD_PLASMA',
		'MOD_PLASMA_SPLASH',
		'MOD_RAILGUN',
		'MOD_LIGHTNING',
		'MOD_BFG',
		'MOD_BFG_SPLASH',
		'MOD_WATER',
		'MOD_SLIME',
		'MOD_LAVA',
		'MOD_CRUSH',
		'MOD_TELEFRAG',
		'MOD_FALLING',
		'MOD_SUICIDE',
		'MOD_TARGET_LASER',
		'MOD_TRIGGER_HURT',
		'MOD_NAIL',
		'MOD_CHAINGUN',
		'MOD_PROXIMITY_MINE',
		'MOD_KAMIKAZE',
		'MOD_JUICED',
		'MOD_GRAPPLE',
	);

	private $logArr;

	public function __construct()
	{
		// Gets the log file content inside an array (each line is an array entry).
		// No parameters.
		// No return.
		$this->logArr = file(self::LOG_FILE_NAME);
		if(!$this->logArr)
		{
			echo("Open file error!\n");
		}
	}

	// Returns the log content.
	// No parameters.
	public function getLogContent()
	{
		return $this->logArr;
	}

	// Make one array to each match.
	// No parameters.
	// Returns an array of arrays containing in each array a match.
	public function getMatchList()
	{
		$matchList = array();
		$isGameOpened = false;
		$currentMatchIndex = 0;

		if(!$this->logArr)
		{
			return false;
		}

		// Get list of matchs from log file.
		foreach($this->logArr as $line)
		{
			// Case there is many 'InitGame' key we will skip until find 'ShutdownGame' key.
			if(!$isGameOpened && strpos($line, self::LOG_KEY_INIT_GAME))
			{
				// Match is opened, lets to to start.
				$isGameOpened = true;
				$matchList[$currentMatchIndex] = array();
			}

			if($isGameOpened)
			{
				// While match is opened we will store lines...
				array_push($matchList[$currentMatchIndex], $line);
			}

			if(strpos($line, self::LOG_KEY_SHUTDOWN_GAME))
			{
				// Match finish, lets to to stop.
				$isGameOpened = false;
				$currentMatchIndex++;
			}
		}

		return $matchList;
	}

	// Returns all kills from one match.
	// array $match An array containing a match.
	// Returns an array with only kills.
	public function getKillsFromMatch(array $match)
	{
		$kills = array();

		if(count($match) == 0)
		{
			return false;
		}

		// Read all match to recover only kill entries.
		foreach($match as $line)
		{
			if(strpos($line, self::LOG_KEY_KILL))
			{
				array_push($kills, $line);
			}
		}

		return $kills;
	}

	// Returns all players from one match.
	// array $match An array containing a match.
	// Returns an array with only players.
	public function getPlayersFromMatch(array $match)
	{
		$players = array();

		if(count($match) == 0)
		{
			return false;
		}

		foreach($match as $line)
		{
			if(strpos($line, self::LOG_KEY_PLAYER))
			{
				// Player example:
				// "3:32 ClientUserinfoChanged: 2 n\Oootsimo\t\0\model\razor/id\hmodel\razor/id\g_redteam\\g_blueteam\\c1\3\c2\5\hc\100\w\0\l\0\tt\0\tl\0"
				//  strpos returns 30             ^
				// We need to recover only 'Oootsimo' from string.
				$position = strpos($line, "n\\");
				$position += 2; // Adding two more bytes (n\).
				$player = "";

				for($i = 0; $line[$position] != "\\"; $i++, $position++)
				{
					$player[$i] = $line[$position];
				}

				array_push($players, $player);
			}
		}

		// Removing duplicated players.
		$players = array_unique($players);

		// Ordening players by name (alphabetical order).
		asort($players);

		return $players;
	}

	// Returns information from one kill (one line from match).
	// string $kill A single line containing a kill.
	// Return an array with the killer, killed and killmode.
	public function getWhoKillWho(string $kill)
	{
		$killer = "";
		$killed = "";
		$killMode = "";

		if(!strlen($kill))
		{
			return false;
		}

		// Kill example:
		// 22:06 Kill: 2 3 7: Isgalamido killed Mocinha by MOD_ROCKET_SPLASH
		$position = strpos($kill, " killed ");
		if($position)
		{
			// Gets the killer player...
			$positionTmp = strrpos($kill, ":");
			$positionTmp += 2;

			for($i = 0; $positionTmp < $position; $i++)
			{
				$killer[$i] = $kill[$positionTmp++];
			}

			// Gets the killed player...
			$position += strlen(" killed ");

			$positionTmp = strpos($kill, " by ");
			for($i = 0; $position < $positionTmp; $i++)
			{
				$killed[$i] = $kill[$position++];
			}

			$positionTmp += 4; // Adding four bytes ( by ).

			// Gets the kill mode...
			for($i = 0; $positionTmp < strlen($kill) - 1; $i++)
			{
				$killMode[$i] = $kill[$positionTmp++];
			}

			return array("killer" => $killer, "killed" => $killed, "killMode" => $killMode);
		}

		return false;
	}

	private function isValidPlayer($player)
	{
		// It is not a player...
		if($player == "totalKills" || $player == self::LOG_KEY_PLAYER_WORLD)
		{
			return false;
		}
		return true;
	}

	private function isValidKillScore(array $ranking)
	{
		$allKills = $ranking["totalKills"];
		$playerKills = 0;

		// The sum of all players kills will be equal to totalKills.
		// Lets go to sum all players kills and the <world> kills, but when <world> kill someone the countage of player that was killed is decremented.
		// So we should mutiplicate <world> countage per two.
		foreach($ranking as $player => $kills)
		{
			if($this->isValidPlayer($player))
			{
				$playerKills += $kills;
			}
		}

		$playerKills += ($ranking[self::LOG_KEY_PLAYER_WORLD] * 2);

		return ($allKills == $playerKills);
	}

	// Gets the kill score from match.
	// array $match An array containing a match.
	// Returns an array with the score or false case error.
	// Output example (using print_r() function):
	// array(6) {
	//   ["Dono da Bola"]=>
	//   int(-1)
	//   ["Mocinha"]=>
	//   int(0)
	//   ["Isgalamido"]=>
	//   int(1)
	//   ["Zeh"]=>
	//   int(-2)
	//   ["<world>"]=>
	//   int(3)
	//   ["totalKills"]=>
	//   int(4)
	// }
	public function getKillScore(array $match)
	{
		$players = array();
		$kills = array();
		$ranking = array();
		$totalKills = 0;

		if(count($match) == 0)
		{
			return false;
		}

		$players = $this->getPlayersFromMatch($match);
		$kills = $this->getKillsFromMatch($match);

		foreach($players as $player)
		{
			// Lets go to create an array with each key is player name and value is the kills quantity.
			$ranking[$player] = 0;
		}

		// Lets go to save the <world> kills...
		$ranking[self::LOG_KEY_PLAYER_WORLD] = 0;

		foreach($kills as $kill)
		{
			$killInfo = $this->getWhoKillWho($kill);

			if($killInfo)
			{
				// If player kill yourself we will not count it...
				if($killInfo["killer"] == $killInfo["killed"])
				{
					continue;
				}

				// We will increment each player kill, but if the player was killed by '<world>' his countage will be decremented.
				if($killInfo["killer"] == self::LOG_KEY_PLAYER_WORLD)
				{
					$ranking[$killInfo["killed"]]--;
					$ranking[self::LOG_KEY_PLAYER_WORLD]++;
				}
				else
				{
					$ranking[$killInfo["killer"]]++;
				}
				$totalKills++;
			}
		}
		// It is not a player, is just the kills countage.
		$ranking["totalKills"] = $totalKills;

		if(!$this->isValidKillScore($ranking))
		{
			// Invalid countage...
			return false;
		}

		return $ranking;
	}

	// Gets the kill score from match in json format.
	// array $match An array containing a match.
	// string $title The title of json output.
	// $jsonOutputMode The json output mode.
	// Returns a json string with the score or false case error.
	public function getKillScoreJson(array $match, string $title = "game_1", $jsonOutputMode = JSON_PRETTY_PRINT)
	{
		$killScoreJson = array("total_kills" => 0, "players" => array(), "kills" => null);
		$killScore = $this->getKillScore($match);
		$resultJson = "";

		if(!$killScore || count($killScore) == 0)
		{
			return false;
		}

		// Output example:
		// game_1: {
		//     total_kills: 45;
		//     players: ["Dono da bola", "Isgalamido", "Zeh"]
		//     kills: {
		//       "Dono da bola": 5,
		//       "Isgalamido": 18,
		//       "Zeh": 20
		//     }
		// }
		$killScoreJson["total_kills"] = $killScore["totalKills"];
		foreach($killScore as $player => $kills)
		{
			if($this->isValidPlayer($player))
			{
				array_push($killScoreJson["players"], $player);
				$killScoreJson["kills"][$player] = $kills;
			}
		}

		$resultJson = json_encode($killScoreJson, $jsonOutputMode);
		$resultJson = $title.": ".$resultJson;

		return $resultJson;
	}

	// Gets the kill mode score from match.
	// array $match An array containing a match.
	// Returns an array with the score of kill modes or false case error.
	public function getKillScoreByKillMode(array $match)
	{
		$kills = array();
		$rankingKillModes = array();

		if(count($match) == 0)
		{
			return false;
		}

		// Create an array with all kills modes.
		foreach(self::KILL_MODES as $killMode)
		{
			$rankingKillModes[$killMode] = 0;
		}

		// Gets all kills from one match.
		$kills = $this->getKillsFromMatch($match);

		// Count kills modes.
		foreach($kills as $kill)
		{
			$killInfo = $this->getWhoKillWho($kill);

			if($killInfo)
			{
				$rankingKillModes[$killInfo["killMode"]]++;
			}
		}

		// Removed keys with kill modes that is not used in the match.
		foreach($rankingKillModes as $killMode => $count)
		{
			if($count == 0)
			{
				unset($rankingKillModes[$killMode]);
			}
		}

		// Ordening kills score in descending order (by quantity).
		arsort($rankingKillModes);

		return $rankingKillModes;
	}

	// Gets the kill mode score from match in json format.
	// array $match An array containing a match.
	// string $title The title of json output.
	// $jsonOutputMode The json output mode.
	// Returns a string with the score of kill modes or false case error.
	public function getKillScoreByKillModeJson(array $match, string $title = "game_1", $jsonOutputMode = JSON_PRETTY_PRINT)
	{
		$killScoreByKillModeJson = array("kills_by_means" => array());
		$killScoreByKillMode = $this->getKillScoreByKillMode($match);
		$resultJson = "";

		if(!$killScoreByKillMode || count($killScoreByKillMode) == 0)
		{
			return false;
		}

		// Output example:
		// "game_1": {
		// 	kills_by_means: {
		// 		"MOD_SHOTGUN": 10,
		// 		"MOD_RAILGUN": 2,
		// 		"MOD_GAUNTLET": 1,
		// 		"XXXX": N
		// 	}
		// }
		foreach($killScoreByKillMode as $killMode => $count)
		{
			$killScoreByKillModeJson["kills_by_means"][$killMode] = $count;
		}

		$resultJson = json_encode($killScoreByKillModeJson, $jsonOutputMode);
		$resultJson = $title.": ".$resultJson;

		return $resultJson;
	}
}
