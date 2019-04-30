-- Creation of database structure...

DROP DATABASE IF EXISTS db_partida_quake;

CREATE DATABASE IF NOT EXISTS db_partida_quake;

CREATE TABLE db_partida_quake.tb_partida(
	id_partida INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(50),
	qtd_kills INTEGER NOT NULL
);

CREATE TABLE db_partida_quake.tb_jogador(
	id_jogador INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	nome VARCHAR(100) NOT NULL,
);

CREATE TABLE db_partida_quake.tb_partida_pontuacao(
	id_partida_pontuacao INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_partida INTEGER NOT NULL,
		FOREIGN KEY (id_partida) REFERENCES tb_partida(id_partida),
	id_jogador INTEGER NOT NULL,
		FOREIGN KEY (id_jogador) REFERENCES tb_jogador(id_jogador),
	qtd_kills INTEGER NOT NULL
);
