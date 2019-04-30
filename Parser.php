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
}
