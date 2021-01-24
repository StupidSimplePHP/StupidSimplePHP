<?php
/**
 * Application.
 */
declare(strict_types = 1);

namespace StupidSimplePhp;

/**
 * Application.
 */
class App
{
	const ENV_LIVE = 'live';
	const ENV_DEV = 'dev';

	/**
	 * Singleton instance.
	 *
	 * @var self
	 */
	private static ?App $_instance;

	/**
	 * Environment (live or dev).
	 *
	 * @var string
	 */
	private string $_env;

	/**
	 * App root directory.
	 *
	 * @var string
	 */
	private string $_rootDir;

	/**
	 * Config instance.
	 *
	 * @var Config
	 */
	private Config $_config;

	/**
	 * Get App instance.
	 *
	 * @return self
	 */
	public static function getInstance(): App
	{
		if (!isset(self::$_instance)) {
			throw new \Exception('App not instantiated');
		}

		return self::$_instance;
	}

	/**
	 * Clear instance, for unit testing.
	 *
	 * @return void
	 */
	public static function clearInstance(): void
	{
		self::$_instance = null;
	}

	/**
	 * Constructor.
	 *
	 * Singleton that can be instantiated normally, but only once.
	 *
	 * @param string $env     The environment.
	 * @param string $rootDir App root directory.
	 *
	 * @throws \Exception If App already created, environment invalid, or root directory invalid.
	 */
	public function __construct(string $env, string $rootDir)
	{
		if (isset(self::$_instance)) {
			throw new \Exception('App instance already created');
		} elseif (!in_array($env, [self::ENV_LIVE, self::ENV_DEV])) {
			throw new \Exception("Unknown environment '$env'");
		} elseif (!is_dir($rootDir)) {
			throw new \Exception("Invalid root directory '$rootDir'");
		}

		$this->_env = $env;
		$this->_rootDir = $rootDir;

		self::$_instance = $this;
	}

	/**
	 * Getter for env.
	 *
	 * @return string
	 */
	public function getEnv(): string
	{
		return $this->_env;
	}

	/**
	 * Getter for rootDir.
	 *
	 * @return string
	 */
	public function getRootDir(): string
	{
		return $this->_rootDir;
	}

	/**
	 * Setter for Config.
	 *
	 * @param Config $config Config instance.
	 *
	 * @return $this
	 */
	public function setConfig(Config $config): App
	{
		$this->_config = $config;
		return $this;
	}

	/**
	 * Getter for Config.
	 *
	 * @return Config
	 */
	public function getConfig(): Config
	{
		return $this->_config;
	}
}
