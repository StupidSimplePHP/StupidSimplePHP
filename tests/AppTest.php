<?php
namespace StupidSimplePhp;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
	public function setUp(): void
	{
		App::clearInstance();
	}

	public function testInstanceExists()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('App instance already created');

		new App(App::ENV_LIVE, __DIR__);
		new App(App::ENV_LIVE, __DIR__);
	}

	public function testInvalidEnv()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Unknown environment 'foo'");

		new App('foo', __DIR__);
	}

	public function testInvalidRootDir()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Invalid root directory 'notExists'");

		new App(App::ENV_DEV, 'notExists');
	}

	public function testGetInstanceNotInstantiated()
	{
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('App not instantiated');

		App::getInstance();
	}

	public function testGetInstance()
	{
		$app = new App(App::ENV_LIVE, __DIR__);

		$this->assertSame($app, App::getInstance());
	}

	public function testGetEnv()
	{
		$app = new App(App::ENV_DEV, __DIR__);

		$this->assertSame(App::ENV_DEV, $app->getEnv());
	}

	public function testGetRootDir()
	{
		$app = new App(App::ENV_DEV, __DIR__);

		$this->assertSame(__DIR__, $app->getRootDir());
	}

	public function testSetGetConfig()
	{
		$config = new Config(['foo' => 'bah']);

		$app = new App(App::ENV_LIVE, __DIR__);

		$this->assertSame($app, $app->setConfig($config));
		$this->assertSame($config, $app->getConfig());
	}
}
