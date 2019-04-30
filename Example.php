<?php

require_once 'Parser.php';

class Example
{
	private static $parser;
	private static $matchs;
	private static $killScoreJson;

	public static function run()
	{
		echo("Kill Score Test\n");

		self::$parser = new Parser();

		self::$matchs = self::$parser->getMatchList();

		foreach(self::$matchs as $key => $match)
		{
			self::$killScoreJson = self::$parser->getKillScoreJson($match, sprintf("game_%d", ($key + 1)));

			if(self::$killScoreJson)
			{
				echo(self::$killScoreJson);
				echo("\n");
			}
		}

		echo("Kill Score Test End\n");
	}
}

Example::run();
