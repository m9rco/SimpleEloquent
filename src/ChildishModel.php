<?php
namespace Childish;

use Childish\database\DatabaseManager;
use Childish\database\ModelManager;
use Childish\support\Collection;
use Childish\support\Tools;

/**
 * ChildishModel
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @version   1.0
 */
abstract class ChildishModel
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * The model attribute's original state.
     *
     * @var array
     */
    protected $original = [];

    /**
     * Indicates if the model was inserted during the current request lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

    /**
     * @var  \Childish\database\DatabaseManager
     */
    protected static $resolver;

    /**
     * The array of booted models.
     *
     * @var array
     */
    protected static $booted = [];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string 是否被删除
     */
    const DELETED_AT = null;

    /**
     * Custom update
     *
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';


    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBootedModels()
    {
        static::$booted = [];
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = new static((array)$attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
    }

    /**
     * Begin querying the model on the write connection.
     *
     * @return \Childish\query\Builder
     */
    public static function onWriteConnection()
    {
        $instance = new static;

        return $instance->newQuery()->useWritePdo();
    }

    /**
     * Get all of the models from the database.
     *
     * @param  array|mixed $columns
     * @return \Childish\support\Collection|static[]
     */
    public static function all($columns = ['*'])
    {
        return self::query()->get(
            is_array($columns) ? $columns : func_get_args()
        );
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->{static::UPDATED_AT} = $value;

        return $this;
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->{static::CREATED_AT} = $value;

        return $this;
    }


    /**
     * Begin querying the model.
     *
     * @return \Childish\query\Builder
     */
    public static function query()
    {
        return (new static)->newQuery();
    }

    /**
     * getConnection
     *
     * @static
     * @return mixed
     */
    public static function getTableConnection()
    {
        return (new static())->getConnection();
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Childish\database\ModelManager
     */
    public function newQuery()
    {
        $builder = new ModelManager($this->newBaseQueryBuilder());
        return $builder->setModel($this);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Childish\query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->table($this->getTable());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array $models
     * @return \Childish\support\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    /**
     * Get the database connection for the model.
     *
     * @return \Childish\connection\Connection
     */
    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Set the connection associated with the model.
     *
     * @param  string $name
     * @return $this
     */
    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * Resolve a connection instance.
     *
     * @param  string|null $connection
     * @return \Childish\connection\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return  \Childish\database\DatabaseManager $resolver
     */
    public static function getConnectionResolver()
    {
        return static::$resolver;
    }

    /**
     * et the connection resolver instance.
     *
     * @static
     * @param \Childish\database\DatabaseManager $resolver
     */
    public static function setConnectionResolver(DatabaseManager $resolver)
    {
        static::$resolver = $resolver;
    }

    /**
     * Unset the connection resolver for models.
     *
     * @return void
     */
    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (!isset($this->table)) {
            return str_replace('\\', '', Tools::snake(Tools::plural(Tools::Basename($this))));
        }

        return $this->table;
    }

    /**
     * Set the table associated with the model.
     *
     * @param  string $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString()
    {
        if (false === static::DATE_FORMAT) {
            return time();
        }
        return date(static::DATE_FORMAT);
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array       $attributes
     * @param  string|null $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance([], true);

        $model->attributes = (array)$attributes;

        $model->setConnection($connection ? : $this->getConnectionName());

        return $model;
    }
}