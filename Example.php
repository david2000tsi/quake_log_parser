<?php

require_once(__DIR__.'/src/Parser.php');
require_once(__DIR__.'/src/Database.php');

class Example
{
	private static $parser;
	private static $matchs;
	private static $killScoreJson;
	private static $killScoreByKillModeJson;

	private static $connDatabase;

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

			self::$killScoreByKillModeJson = self::$parser->getKillScoreByKillModeJson($match, sprintf("game_%d", ($key + 1)));

			if(self::$killScoreByKillModeJson)
			{
				echo(self::$killScoreByKillModeJson);
				echo("\n");
			}
		}

		echo("Kill Score Test End\n");
	}

	public static function createDatabaseAndAddData()
	{
		echo("Save Data To Database Test\n");

		self::$parser = new Parser();
		self::$connDatabase = Database::getInstance();

		self::$matchs = self::$parser->getMatchList();

		foreach(self::$matchs as $key => $match)
		{
			$killScore = self::$parser->getKillScore($match);
			if($killScore)
			{
				$matchName = sprintf("game_%d", ($key + 1));
				$qtdKills = $killScore["totalKills"];

				echo("Saving [".$matchName."]\n");

				$result = self::$connDatabase->runQuery("INSERT INTO ".Database::DB_TABLE_MATCH."(nome, qtd_kills) VALUES('".$matchName."', ".$qtdKills.")");
				if(!$result)
				{
					echo("Error at insert values, skipping match...\n");
					continue;
				}
				$matchInsertId = self::$connDatabase->getLastInsertId();

				foreach($killScore as $player => $kills)
				{
					// It is not a player...
					if($player == "totalKills")
					{
						continue;
					}

					// Lets go to check if palyer already exists in the database.
					// Case new user we will insert into database.
					$result = self::$connDatabase->runQueryWithResult("SELECT id_jogador FROM tb_jogador WHERE nome = '".$player."' LIMIT 1");
					if($result && count($result) > 0)
					{
						$playerInsertId = $result[0]["id_jogador"];
					}
					else
					{
						self::$connDatabase->runQuery("INSERT INTO ".Database::DB_TABLE_PLAYER."(nome) VALUES('".$player."')");
						$playerInsertId = self::$connDatabase->getLastInsertId();
					}

					self::$connDatabase->runQuery("INSERT INTO ".Database::DB_TABLE_MATCH_INFORMATION."(id_partida, id_jogador, qtd_kills) VALUES(".$matchInsertId.", ".$playerInsertId.", ".$kills.")");
				}
			}
		}
		echo("Save Data To Database End\n");
	}
}

Example::run();

Example::createDatabaseAndAddData();
