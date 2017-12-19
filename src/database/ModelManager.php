<?php
namespace Childish\database;

use Childish\ChildishModel;
use Childish\query\Builder;
use Childish\support\Tools;

/**
 * ModelManager
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/13
 * @package   Childish\database
 * @version   1.0
 */
class ModelManager
{
    /**
     * The base query builder instance.
     *
     * @var \Childish\query\Builder
     */
    protected $query;

    /**
     * The model being queried.
     *
     * @var \Childish\ChildishModel
     */
    protected $model;

    /**
     * The query union statements.
     *
     * @var array
     */
    public $unions;
    /**
     * The methods that should be returned from query builder.
     *
     * @var array
     */
    protected $passthru = [
        'insert', 'insertGetId', 'getBindings', 'toSql',
        'exists', 'count', 'min', 'max', 'avg', 'sum', 'getConnection',
    ];


    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Childish\query\Builder $query
     * @return mixed|void
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param  \Childish\ChildishModel $model
     * @return $this
     */
    public function setModel(ChildishModel $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        return $this;
    }

    /**
     * Get the model instance being queried.
     *
     * @return \Childish\ChildishModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * update
     *
     * @param $values
     * @return int
     */
    public function update($values)
    {
        return $this->getQuery()->update($this->addUpdatedAtColumn($values));
    }

    /**
     * insert
     *
     * @param $values
     * @return int
     */
    public function insert($values)
    {
        return $this->getQuery()->insert($this->addInsertedAtColumn($values));
    }

    /**
     * insertGetId
     *
     * @param $values
     * @return int
     */
    public function insertGetId($values)
    {
        return $this->getQuery()->insertGetId($this->addInsertedAtColumn($values));
    }

    /**
     * updateOrInsert
     *
     * @param $values
     * @return bool
     */
    public function updateOrInsert($values)
    {
        return $this->getQuery()->updateOrInsert($this->addInsertedAtColumn($values));
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string $column
     * @return mixed
     */
    public function value($column)
    {
        if ($result = $this->first([$column])) {
            return $result->{$column};
        }
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array $columns
     * @return \Childish\support\Collection|static[]\
     */
    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param  array $columns
     * @return \Childish\ChildishModel[]
     */
    public function getModels($columns = ['*'])
    {
        return $this->model->hydrate(
            $this->query->get($columns)->all()
        )->all();
    }

    /**
     * Create a collection of models from plain arrays.
     *
     * @param  array $items
     * @return \Childish\support\Collection
     */
    public function hydrate(array $items)
    {
        $instance = $this->newModelInstance();

        return $instance->newCollection(array_map(function ($item) use ($instance) {
            return $instance->newFromBuilder($item);
        }, $items));
    }

    /**
     * Create a new instance of the model being queried.
     *
     * @param  array $attributes
     * @return \Childish\ChildishModel
     */
    public function newModelInstance($attributes = [])
    {
        return $this->model->newInstance($attributes)->setConnection(
            $this->query->getConnection()->getName()
        );
    }


    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @return \Childish\ChildishModel|static|null
     */
    public function first($columns = ['*'])
    {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int $value
     * @return \Childish\query\Builder|static
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param  int $value
     * @return $this
     */
    public function limit($value)
    {
        $property = $this->unions ? 'unionLimit' : 'limit';

        if ($value >= 0) {
            $this->$property = $value;
        }

        return $this;
    }


    /**
     * Get the underlying query builder instance.
     *
     * @return \Childish\query\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param  \Childish\query\Builder $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }


    /**
     * Add the "updated at" column to an array of values.
     *
     * @param  array $values
     * @return array
     */
    protected function addUpdatedAtColumn(array $values)
    {
        if (!$this->model->timestamps) {
            return $values;
        }

        return Tools::add(
            $values, $this->model->getUpdatedAtColumn(),
            $this->model->freshTimestampString()
        );
    }

    /**
     * Add the "create_time at" column to an array of values.
     *
     * @param array $values
     * @return array
     */
    protected function addInsertedAtColumn(array $values)
    {
        if (!$this->model->timestamps) {
            return $values;
        }

        return Tools::add(
            $values, $this->model->getCreatedAtColumn(),
            $this->model->freshTimestampString()
        );
    }


    /**
     * Increment a column's value by a given amount.
     *
     * @param  string $column
     * @param  int    $amount
     * @param  array  $extra
     * @return int
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        return $this->getQuery()->increment(
            $column, $amount, $this->addUpdatedAtColumn($extra)
        );
    }

    /**
     * Delete a record from the database.
     *
     * @return mixed
     */
    public function delete()
    {
        return $this->getQuery()->delete();
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->passthru)) {
            return $this->getQuery()->{$method}(...$parameters);
        }
        $this->query->{$method}(...$parameters);
        return $this;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }
}