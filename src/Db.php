<?php
/**
 * Db class.
 */
declare(strict_types = 1);

namespace StupidSimplePhp;

/**
 * Db class.
 */
class Db
{
	/**
	 * Array of database connection parameters.
	 *
	 * @var array
	 */
	private static array $_config;

	/**
	 * Array of PDO instances.
	 *
	 * @var \PDO[]
	 */
	private static array $_instances = [];

	/**
	 * Database connection name (db config key).
	 *
	 * @var string
	 */
	private string $_dbKey;

	/**
	 * PDO instance.
	 *
	 * @var \PDO
	 */
	private \PDO $_pdo;

	/**
	 * Set database connection parameters.
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	public static function setConfig(array $config): void
	{
		self::$_config = $config;
	}

	/**
	 * Clear all database connection instances.
	 *
	 * @return void
	 */
	public static function clearInstances(): void
	{
		self::$_instances = [];
	}

	/**
	 * Fetch/create database connection.
	 *
	 * @param string $dbKey Connection name.
	 *
	 * @return $this
	 */
	public static function newDb(string $dbKey): Db
	{
		if (!isset(self::$_instances[$dbKey])) {
			self::$_instances[$dbKey] = new self($dbKey);
		}
		return self::$_instances[$dbKey];
	}

	/**
	 * Object constructor.
	 *
	 * @param string $dbKey Connection name.
	 */
	protected function __construct(string $dbKey)
	{
		$this->_dbKey = $dbKey;
	}

	/**
	 * Set PDO instance to use (for unit testing).
	 *
	 * @param \PDO $pdo PDO instance.
	 *
	 * @return $this
	 */
	public function setPdo(\PDO $pdo): Db
	{
		$this->_pdo = $pdo;
		return $this;
	}

	/**
	 * Magic __call method.
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Method arguments.
	 *
	 * @return mixed
	 */
	public function __call(string $name, array $arguments)
	{
		$pdo = $this->_getPdo();
		if (!method_exists($pdo, $name)) {
			$pdo->$name();
		}
		return call_user_func_array([$pdo, $name], $arguments);
	}

	/**
	 * Magic __callStatic method.
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Method arguments.
	 *
	 * @return mixed
	 */
	public static function __callStatic(string $name, array $arguments)
	{
		return call_user_func_array([\PDO::class, $name], $arguments);
	}

	/**
	 * Get config for specific database connection.
	 *
	 * @param string $dbKey Connection name.
	 * @return array
	 * @throws \Exception
	 */
	private static function _getDbConfig(string $dbKey): array
	{
		if (!isset(self::$_config)) {
			throw new \Exception('Config not set');
		}
		return self::$_config['databases'][$dbKey];
	}

	/**
	 * Get PDO instance via lazy-loading.
	 *
	 * @return \PDO
	 * @throws \Exception If DSN not defined.
	 */
	private function _getPdo(): \PDO
	{
		if (!isset($this->_pdo)) {
			$dbConfig = self::_getDbConfig($this->_dbKey);
			if (empty($dbConfig['dsn'])) {
				throw new \Exception("PDO DSN not defined for database '$this->_dbKey'");
			}

			$username = $dbConfig['username'] ?? null;
			$password = $dbConfig['password'] ?? null;
			$options = $dbConfig['options'] ?? null;

			$this->_pdo = new \PDO($dbConfig['dsn'], $username, $password, $options);

			if (isset(self::$_config['attributes'])) {
				foreach (self::$_config['attributes'] as $attribute => $value) {
					$this->_pdo->setAttribute($attribute, $value);
				}
			}
		}
		return $this->_pdo;
	}
}
