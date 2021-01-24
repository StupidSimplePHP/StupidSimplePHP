<?php
namespace StupidSimplePhp;

use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
	protected $_config = [
		'databases' => [
			'test' => [
				'dsn' => 'sqlite::memory:',
			],
		],
		'attributes' => [
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		],
	];

	public function setUp(): void
	{
		Db::clearInstances();
	}

	public function testConfigNotSet()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Config not set');

		Db::newDb('foo')->query('bar');
	}

	public function testCallMethodNotExists()
	{
		Db::setConfig($this->_config);
		$db = Db::newDb('test');

		$this->expectErrorMessage('Call to undefined method PDO::notExists()');
		$db->notExists();
	}

	public function testCall()
	{
		Db::setConfig($this->_config);

		$pdo = $this->getMockBuilder(\PDO::class)
			->disableOriginalConstructor()
			->setMethods(['query'])
			->getMock();

		$pdo->expects($this->once())->method('query')->with('bar');

		$db = Db::newDb('blah');
		$db->setPdo($pdo);
		$db->query('bar');
	}

	public function testCallStatic()
	{
		$drivers = Db::getAvailableDrivers();
		$this->assertIsArray($drivers);
		$this->assertNotEmpty($drivers);
	}

	public function testReal()
	{
		Db::setConfig($this->_config);
		$db = Db::newDb('test');

		$result = $db->query('SELECT 1 AS num')->fetchAll();
		$this->assertEquals([0 => ['num' => '1']], $result);
	}

	public function testReuse()
	{
		Db::setConfig($this->_config);
		$dbOne = Db::newDb('test');
		$dbTwo = Db::newDb('test');

		$this->assertSame($dbOne, $dbTwo);
	}
}
