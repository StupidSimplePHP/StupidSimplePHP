<?php
namespace StupidSimplePhp;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	private $_data = [
		'foo' => [
			'bar' => 'value',
		],
	];

	public function testGetNotSet()
	{
		$config = new Config([]);

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Config not set for 'notSet'");

		$config->notSet;
	}

	public function testGetConfig()
	{
		$config = new Config($this->_data);

		$this->assertInstanceOf(Config::class, $config->foo);
	}

	public function testGet()
	{
		$config = new Config($this->_data);

		$this->assertSame('value', $config->foo->bar);
	}

	public function testGetObject()
	{
		$data = ['foo' => (object) ['bar' => 'value']];
		$config = new Config($data);

		$object = $config->foo;
		$this->assertInstanceOf(\stdClass::class, $object);
		$this->assertSame($data['foo'], $object);
	}

	public function testToArray()
	{
		$config = new Config($this->_data);

		$array = $config->foo->toArray();
		$this->assertIsArray($array);
		$this->assertSame($this->_data['foo'], $array);
	}
}
