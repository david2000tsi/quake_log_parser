<?php

require_once 'Database.php';

use PHPUnit\Framework\TestCase;

final class DatabaseTest extends TestCase
{
	public function testDatabaseStructure()
	{
		$this->assertEquals(Database::generateDatabaseStructure(), true);
	}

	public function testDatabaseConnection()
	{
		$instance = Database::getInstance();

		var_dump($instance);

		$this->assertEquals($instance instanceof Database, true);
	}

	public function testDatabaseRunQuery()
	{
		$instance = Database::getInstance();

		$this->assertEquals($instance instanceof Database, true);

		$matchName = 'partida_1';
		$kills = 100;

		$result = $instance->runQuery("INSERT INTO ".Database::DB_TABLE_MATCH."(nome, qtd_kills) VALUES('".$matchName."', ".$kills.")");
		$this->assertEquals($result, true);

		$result = $instance->runQueryWithResult("SELECT nome, qtd_kills FROM ".Database::DB_TABLE_MATCH." ORDER BY id_partida DESC LIMIT 1");
		$this->assertEquals(empty($result), false);

		$this->assertEquals($result[0]["nome"], $matchName);
		$this->assertEquals($result[0]["qtd_kills"], $kills);

		$result = $instance->runQuery("DELETE FROM ".Database::DB_TABLE_MATCH);
		$this->assertEquals($result, true);
	}
}
