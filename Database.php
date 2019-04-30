<?php

class Database
{
	// Database connection data.
	CONST DB_HOST = "127.0.0.1";
	CONST DB_PORT = "3306";
	CONST DB_USER = "root"; // Using root user for local connections...
	CONST DB_PASSWORD = "mysql";

	// Database structure.
	CONST DB_NAME = "db_partida_quake";
	CONST DB_TABLE_MATCH = "tb_partida";
	CONST DB_TABLE_PLAYER = "tb_jogador";
	CONST DB_TABLE_MATCH_INFORMATION = "tb_partida_pontuacao";

	private static $connServer;
	private static $connDatabase;
	private static $instance;

	private function __construct()
	{
		try
		{
			// Lets go to open connection with server (using no database), create database structure before open connection with database.
			// Will be executed only once, in the first call of the constructor.
			self::generateDatabaseStructure();

			// Open a connection with specific database.
			self::$connDatabase = new PDO("mysql:host=".self::DB_HOST.";dbname=".self::DB_NAME.";port=".self::DB_PORT.";sslmode=prefer", self::DB_USER, self::DB_PASSWORD);
			self::$connDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			self::$instance = null;
			throw $e;
		}
	}

	// Returns an instance of Database class.
	public static function getInstance()
	{
		if(empty(self::$instance))
		{
			self::$instance = new Database();
		}

		return self::$instance;
	}

	// Execute a query.
	// string $query The query to be executed.
	// Returns true case success or false case error.
	public function runQuery(string $query)
	{
		$result = false;

		try
		{
			$stm = self::$connDatabase->prepare($query);
			$result = $stm->execute();
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}

	// Execute a query returning the results.
	// string $query The query to be executed.
	// Returns query response case success or false case error.
	public function runQueryWithResult(string $query)
	{
		$result = false;

		try
		{
			$stm = self::$connDatabase->prepare($query);
			if($stm->execute())
			{
				$result = array();
				while($res = $stm->fetch(PDO::FETCH_ASSOC))
				{
					array_push($result, $res);
				}
			}
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}

	//*******************************************
	// Database Structure methods...
	//*******************************************

	// Execute a query directly in the server.
	// string $query The query to be executed.
	// Returns true case success or false case error.
	private static function runQueryInternal(string $query)
	{
		$result = false;

		try
		{
			$stm = self::$connServer->prepare($query);
			$result = $stm->execute();
		}
		catch(PDOException $e)
		{
			throw $e;
		}

		return $result;
	}

	// Create a database structure with tables and columns.
	public static function generateDatabaseStructure()
	{
		$result = true;

		try
		{
			self::$connServer = new PDO("mysql:host=127.0.0.1;port=3306;sslmode=prefer", self::DB_USER, self::DB_PASSWORD);
			self::$connServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			self::runQueryInternal("DROP DATABASE IF EXISTS ".self::DB_NAME);

			self::runQueryInternal("CREATE DATABASE IF NOT EXISTS ".self::DB_NAME);

			self::runQueryInternal("CREATE TABLE ".self::DB_NAME.".tb_partida(id_partida INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(50), qtd_kills INTEGER NOT NULL)");

			self::runQueryInternal("CREATE TABLE ".self::DB_NAME.".tb_jogador(id_jogador INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(100) NOT NULL)");

			self::runQueryInternal("CREATE TABLE ".self::DB_NAME.".tb_partida_pontuacao(id_partida_pontuacao INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, id_partida INTEGER NOT NULL, FOREIGN KEY (id_partida) REFERENCES tb_partida(id_partida), id_jogador INTEGER NOT NULL, FOREIGN KEY (id_jogador) REFERENCES tb_jogador(id_jogador), qtd_kills INTEGER NOT NULL)");
		}
		catch(PDOException $e)
		{
			echo("ERROR: CODE(".$e->getCode().") MSG[".$e->getMessage()."]");
			$result = false;
		}
		finally
		{
			self::$connServer = null;
		}

		return $result;
	}
}
