<?php
namespace Childish\connection;

use Childish\support\Tools;
use PDO;
/**
 * Connector
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @package   Childish\connection
 * @version   1.0
 */
class Connector
{

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * Create a new PDO connection.
     *
     * @param  string  $dsn
     * @param  array   $config
     * @param  array   $options
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options)
    {
        list($username, $password) = [
            $config['username'] ?? null, $config['password'] ?? null,
        ];

        try {
            return $this->createPdoConnection(
                $dsn, $username, $password, $options
            );
        } catch (\Exception $e) {
            return $this->tryAgainIfCausedByLostConnection(
                $e, $dsn, $username, $password, $options
            );
        }
    }

    /**
     * Create a new PDO connection instance.
     *
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array  $options
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options)
    {
        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Determine if the connection is persistent.
     *
     * @param  array  $options
     * @return bool
     */
    protected function isPersistentConnection($options)
    {
        return isset($options[PDO::ATTR_PERSISTENT]) &&
               $options[PDO::ATTR_PERSISTENT];
    }

    /**
     * Handle an exception that occurred during connect execution.
     *
     * @param  \Exception  $e
     * @param  string  $dsn
     * @param  string  $username
     * @param  string  $password
     * @param  array   $options
     * @return \PDO
     *
     * @throws \Exception
     */
    protected function tryAgainIfCausedByLostConnection(\Exception $e, $dsn, $username, $password, $options)
    {
        if ($this->causedByLostConnection($e)) {
            return $this->createPdoConnection($dsn, $username, $password, $options);
        }

        throw $e;
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function getOptions(array $config)
    {
        $options = $config['options'] ?? [];

        return array_diff_key($this->options, $options) + $options;
    }

    /**
     * Get the default PDO connection options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->options;
    }

    /**
     * Set the default PDO connection options.
     *
     * @param  array  $options
     * @return void
     */
    public function setDefaultOptions(array $options)
    {
        $this->options = $options;
    }
    /**
     * Determine if the given exception was caused by a lost connection.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function causedByLostConnection(\Exception $e)
    {
        $message = $e->getMessage();
        return Tools::contains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'Transaction() on null',
            'child connection forced to terminate due to client_idle_limit',
        ]);
    }
}