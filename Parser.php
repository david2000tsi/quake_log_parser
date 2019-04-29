<?php

class Parser
{
	// Constants.
	CONST LOG_FILE_NAME = "games.log";
	CONST LOG_KEY_INIT_GAME = "InitGame";
	CONST LOG_KEY_SHUTDOWN_GAME = "ShutdownGame";

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
}
