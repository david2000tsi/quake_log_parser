<?php

require_once 'Parser.php';

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
	private $parser;

	// Instantiates Parser class.
	public function setUp()
	{
		$this->parser = new Parser();
	}

	// Destroys Parser class.
	public function tearDown()
	{
		$this->parser = null;
	}

	// Load all file content.
	public function testLoadLogContent()
	{
		$logContent = $this->parser->getLogContent();
		$type = gettype($logContent);

		$this->assertEquals($type, "array");
	}

	// List matches in subarrays.
	public function testGetMatchList()
	{
		$matchList = $this->parser->getMatchList();
		$type = gettype($matchList);

		$this->assertEquals($type, "array");
	}

	// Gets all kills from one match
	public function testGetKillsFromMatch()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$kills = $this->parser->getKillsFromMatch($matchList[5]);
			$type = gettype($kills);

			$this->assertEquals($type, "array");
		}
	}

	// Gets all players from one match
	public function testGetPlayersFromMatch()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$players = $this->parser->getPlayersFromMatch($matchList[5]);
			$type = gettype($players);

			$this->assertEquals($type, "array");
		}
	}

	// Gets kill information from one kill (line from log).
	public function testGetKillsInfo()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$kills = $this->parser->getKillsFromMatch($matchList[5]);
			$type = gettype($matchList);

			$this->assertEquals($type, "array");

			if(count($kills))
			{
				$killInfo = $this->parser->getWhoKillWho($kills[0]);
				$type = gettype($killInfo);

				$this->assertEquals($type, "array");
			}
		}
	}

	// Gets the kill score from match.
	public function testGetKillScore()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$killScore = $this->parser->getKillScore($matchList[5]);
			$type = gettype($killScore);

			$this->assertEquals($type, "array");
		}
	}

	// Gets the kill score from match in json format.
	public function testGetKillScoreJson()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$killScoreJson = $this->parser->getKillScoreJson($matchList[5]);
			$type = gettype($killScoreJson);

			$this->assertEquals($type, "string");
		}
	}

	// Gets the kill score by kill mode from match.
	public function testGetKillScoreByKillMode()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$killScoreByMode = $this->parser->getKillScoreByKillMode($matchList[5]);
			$type = gettype($killScoreByMode);

			$this->assertEquals($type, "array");
		}
	}

	// Gets the kill score by kill mode from match in json format.
	public function testGetKillScoreByKillModeJson()
	{
		$matchList = $this->parser->getMatchList();
		if(count($matchList))
		{
			$killScoreByMode = $this->parser->getKillScoreByKillModeJson($matchList[5]);
			$type = gettype($killScoreByMode);

			$this->assertEquals($type, "string");
		}
	}
}
