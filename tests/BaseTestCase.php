<?php
namespace Tests;

/**
 * BaseTestCase
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @version   1.0
 * @license   MIT
 */
class BaseTestCase extends \PHPUnit\Framework\TestCase
{
    const DB_TEST_TABLE = 'test';
    const DB_PRACTICE   = 'practice';

    /**
     * BaseTestCase constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        require dirname(__DIR__) . '/vendor/autoload.php';
    }

    /**
     * 获取初始化配置
     *
     * @return array
     */
    public function getDefaultConfig()
    {
        return array (
            /**
             *  SMART DATABASES
             */
            'smart' => array (
                'host'      => "127.0.0.1",
                'write'     => array (
                    'port'     => 3307,
                    'password' => 'smart_master'
                ),
                'read'      => array (
                    array (
                        'port'     => 3308,
                        'password' => 'smart_slave_one'
                    ),
                    array (
                        'port'     => 3309,
                        'password' => 'smart_slave_two'
                    ),
                ),
                'driver'    => 'mysql',
                'database'  => "db_master_test",
                'username'  => "root",
                'password'  => "master_test",
                'charset'   => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix'    => 'smart_db_',
                'strict'    => false,
                'engine'    => null,
            ),
            /**
             *  BIZ DATABASES
             */
            'biz'   => array (
                'host'      => "127.0.0.1",
                'port'      => 3310,
                'password'  => 'biz_master',
                'driver'    => 'mysql',
                'database'  => 'db_biz_test',
                'username'  => 'root',
                'charset'   => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix'    => 'biz_',
                'strict'    => false,
                'engine'    => null,
            ),
        );
    }

    /**
     * 获取随机字符串
     *
     * @param int    $length
     * @param string $type
     * @param int    $convert
     * @return string
     */
    function random($length = 6, $type = 'string', $convert = 0)
    {
        $config = array (
            'number' => '1234567890',
            'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
            'all'    => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        );

        if (!isset($config[$type])) {
            $type = 'string';
        }
        $string = $config[$type];

        $code   = '';
        $strlen = strlen($string) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $string{mt_rand(0, $strlen)};
        }
        if (!empty($convert)) {
            $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
        }
        return $code;
    }
}
