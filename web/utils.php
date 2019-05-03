<?php
	// Opens connection with database.
	// Returns a valid connection or false case error.
	function getConn()
	{
		$connDatabase = false;

		try
		{
			$connDatabase = new PDO("mysql:host=127.0.0.1;dbname=db_partida_quake;port=3306;sslmode=prefer", "root", "mysql");
			$connDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			$connDatabase = false;
		}

		return $connDatabase;
	}

	// Gets the sum of kills of each player in the database.
	// Returns a valid array with kills scores or array with 'error' key.
	function getAllPLayersScore()
	{
		$connDatabase = getConn();

		if(!$connDatabase)
		{
			return ['error' => 'failed'];
		}

		$query = "select jogador.id_jogador as id_jogador, jogador.nome as nome_jogador, sum(pontuacao.qtd_kills) as sum_qtd_kills from tb_jogador jogador, tb_partida_pontuacao pontuacao where jogador.id_jogador = pontuacao.id_jogador and jogador.nome != '<world>' group by id_jogador order by sum_qtd_kills desc";

		$stm = $connDatabase->prepare($query);
		$result = $stm->execute();
		$connDatabase = null;
		if($result)
		{
			return $stm->fetchAll();
		}

		return ['error' => 'failed'];
	}

	// Gets sun of kills of specific player.
	// $playerName The player name.
	// Returns a valid array with kill score of informet player or array with 'error' key.
	function getPlayerScore($playerName)
	{
		$connDatabase = getConn();

		if(!$connDatabase)
		{
			return ['error' => 'failed'];
		}

		$query = "select jogador.id_jogador as id_jogador, jogador.nome as nome_jogador, sum(pontuacao.qtd_kills) as sum_qtd_kills from tb_jogador jogador, tb_partida_pontuacao pontuacao where jogador.id_jogador = pontuacao.id_jogador and jogador.nome != '<world>' and jogador.nome like :nome group by id_jogador order by sum_qtd_kills desc limit 1";

		$stm = $connDatabase->prepare($query);
		$stm->bindValue(':nome', $playerName);
		$result = $stm->execute();
		$connDatabase = null;
		if($result)
		{
			$result = $stm->fetchAll();
			if(count($result))
			{
				return $result[0];
			}
		}

		return ['error' => 'failed'];
	}

	// Handle post request from player form.
	if(isset($_POST))
	{
		switch($_POST["moderequest"])
		{
			case "getplayerscore":
				echo json_encode(getPlayerScore($_POST["nomejogador"]));
				break;
			default:
				break;
		}
	}
?>
