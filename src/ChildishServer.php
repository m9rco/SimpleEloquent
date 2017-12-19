<?php
namespace Childish;

use PDO;
use Childish\database\DatabaseManager;
use Childish\connection\ConnectionFactory;
use Childish\support\Container;
use Childish\support\Fluent;

/**
 * ChildishServer
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/6
 * @package   App\db
 * @version   1.0
 */
class ChildishServer
{
    /**
     * @var \Childish\database\DatabaseManager $manager
     */
    protected $manager;

    /**
     * The current globally used instance.
     *
     * @var ChildishServer
     */
    protected static $instance;

    /**
     * @var \Childish\support\Container $container
     */
    protected $container;

    /**
     * ChildishServer constructor.
     */
    public function __construct()
    {
        $this->setupContainer(new Container);
        $this->setupDefaultConfiguration();
        $this->setupManager();
    }

    /**
     * setupContainer
     *
     * @param \Childish\support\Container $container
     */
    protected function setupContainer(Container $container)
    {
        $this->container = $container;
        if (!$this->container->bound('config')) {
            $this->container->instance('config', new Fluent);
        }
    }

    /**
     * Make this capsule instance available globally.
     *
     * @return void
     */
    public function setAsGlobal()
    {
        static::$instance = $this;
    }

    /**
     * Get the IoC container instance.
     *
     * @return \Childish\support\Container $container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Setup the default database configuration options.
     *
     * @return void
     */
    protected function setupDefaultConfiguration()
    {
        $this->container['config']['database.fetch'] = PDO::FETCH_OBJ;

        $this->container['config']['database.default'] = 'default';
    }

    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $factory       = new ConnectionFactory($this->container);
        $this->manager = new DatabaseManager($this->container, $factory);
    }

    /**
     * Get a connection instance from the global manager.
     *
     * @param  string $connection
     * @return \Childish\connection\Connection
     */
    public static function connection($connection = null)
    {
        return static::$instance->getConnection($connection);
    }

    /**
     * Get a fluent query builder instance.
     *
     * @param  string $table
     * @param  string $connection
     * @return  \Childish\query\Builder;
     */
    public static function table($table, $connection = null)
    {
        return static::$instance->connection($connection)->table($table);
    }

    /**
     * @param  string $name
     * @return \\Connection
     */
    /**
     * Get a registered connection instance.
     *
     * @param null $name
     * @return \Childish\connection\Connection
     */
    public function getConnection($name = null)
    {
        return $this->manager->connection($name);
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array  $config
     * @param  string $name
     * @return void
     */
    public function addConnection(array $config, $name = 'default')
    {
        $connections = $this->container['config']['database.connections'];

        $connections[$name] = $config;

        $this->container['config']['database.connections'] = $connections;
    }

    /**
     * Set the fetch mode for the database connections.
     *
     * @param  int $fetchMode
     * @return $this
     */
    public function setFetchMode($fetchMode)
    {
        $this->container['config']['database.fetch'] = $fetchMode;

        return $this;
    }

    /**
     * Get the database manager instance.
     *
     * @return mixed
     */
    public function getDatabaseManager()
    {
        return $this->manager;
    }

    /**
     * bootEloquent
     *
     * @return void
     */
    public function bootEloquent()
    {
        ChildishModel::setConnectionResolver($this->manager);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::connection()->$method(...$parameters);
    }
}