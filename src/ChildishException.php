<?php
namespace Childish;

use Childish\support\Tools;

/**
 * ChildishException
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @package   Childish
 * @version   1.0
 */
class ChildishException extends \PDOException
{
    /**
     * The SQL for the query.
     *
     * @var string
     */
    protected $sql;

    /**
     * The bindings for the query.
     *
     * @var array
     */
    protected $bindings;

    /**
     * Create a new query exception instance.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @param  \Exception $previous
     * @return void
     */
    public function __construct($sql, array $bindings, $previous)
    {
        parent::__construct('', 0, $previous);

        $this->sql = $sql;
        $this->bindings = $bindings;
        $this->code = $previous->getCode();
        $this->message = $this->formatMessage($sql, $bindings, $previous);

        if ($previous instanceof \PDOException) {
            $this->errorInfo = $previous->errorInfo;
        }
    }

    /**
     * Format the SQL error message.
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @param  \Exception $previous
     * @return string
     */
    protected function formatMessage($sql, $bindings, $previous)
    {
        return $previous->getMessage().' (SQL: '.Tools::replaceArray('?', $bindings, $sql).')';
    }

    /**
     * Get the SQL for the query.
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Get the bindings for the query.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}