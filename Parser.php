<?php

class Parser
{
	// Constants.
	CONST LOG_FILE_NAME = "games.log";
	CONST LOG_KEY_INIT_GAME = "InitGame";
	CONST LOG_KEY_SHUTDOWN_GAME = "ShutdownGame";
	CONST LOG_KEY_KILL = "Kill";
	CONST LOG_KEY_PLAYER = "ClientUserinfoChanged";

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

		foreach($this->logArr as $line)
		{
			if(strpos($line, self::LOG_KEY_INIT_GAME))
			{
				$isGameOpened = true;
				$matchList[$currentMatchIndex] = array();
			}

			if($isGameOpened)
			{
				array_push($matchList[$currentMatchIndex], $line);
			}

			if(strpos($line, self::LOG_KEY_SHUTDOWN_GAME))
			{
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

		// 22:06 Kill: 2 3 7: Isgalamido killed Mocinha by MOD_ROCKET_SPLASH
		$position = strpos($kill, " killed ");
		if($position)
		{
			// Gets the killer...
			$positionTmp = strrpos($kill, ":");
			$positionTmp += 2;

			for($i = 0; $positionTmp < $position; $i++)
			{
				$killer[$i] = $kill[$positionTmp++];
			}

			// Gets the killed.
			$position += strlen(" killed ");

			$positionTmp = strpos($kill, " by ");
			for($i = 0; $position < $positionTmp; $i++)
			{
				$killed[$i] = $kill[$position++];
			}

			$positionTmp += 4; // Adding two bytes ( by ).

			for($i = 0; $positionTmp < strlen($kill); $i++)
			{
				$killMode[$i] = $kill[$positionTmp++];
			}

			return array("killer" => $killer, "killed" => $killed, "killMode" => $killMode);
		}

		return false;
	}

	// Gets the kill score from match.
	// string $kill A single line containing a kill.
	// Returns an array with the score or false case error.
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
			$ranking[$player] = 0;
		}

		foreach($kills as $kill)
		{
			$killInfo = $this->getWhoKillWho($kill);

			if($killInfo)
			{
				// We will increment each player kill, but if the player was killed by '<world>' his countage will be decremented.
				if($killInfo["killer"] == "<world>")
				{
					$ranking[$killInfo["killed"]]--;
				}
				else
				{
					$ranking[$killInfo["killer"]]++;
				}
				$totalKills++;
			}
			$ranking["totalKills"] = $totalKills;
		}

		return $ranking;
	}

	// Gets the kill score from match in json format.
	// string $kill A single line containing a kill.
	// Returns a json string with the score or false case error.
	public function getKillScoreJson(array $match, string $title = "game")
	{
		$killScoreJson = array("total_kills" => 0, "players" => array(), "kills" => null);
		$killScore = $this->getKillScore($match);

		if(count($killScore) == 0)
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
		$killScoreJson["total_kills"] = isset($killScore["totalKills"]) ? $killScore["totalKills"] : 0;
		foreach($killScore as $player => $kills)
		{
			if($player == "totalKills")
			{
				continue;
			}

			array_push($killScoreJson["players"], $player);
			$killScoreJson["kills"][$player] = $kills;
		}

		$result = array($title => $killScoreJson);
		return json_encode($result, JSON_PRETTY_PRINT);
	}
}
