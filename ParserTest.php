<?php

require_once 'Parser.php';

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
	private $parser;

	public function setUp()
	{
		$this->parser = new Parser();
	}

	public function tearDown()
	{
		$this->parser = null;
	}

	public function testLoadLogContent()
	{
		$logContent = $this->parser->getLogContent();
		$type = gettype($logContent);

		$this->assertEquals($type, "array");
	}

	public function testGetMatchList()
	{
		$matchList = $this->parser->getMatchList();
		$type = gettype($matchList);

		$this->assertEquals($type, "array");
	}
}
