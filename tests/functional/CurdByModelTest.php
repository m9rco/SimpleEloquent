<?php
namespace Tests\functional;

use Childish\ChildishModel;
use Tests\BaseTestCase;
use Childish\ChildishServer;

/**
 * CurdByModel
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @package   Tests\functional
 * @version   1.0
 */
class CurdByModelTest extends BaseTestCase
{
    /**
     * @var \Childish\ChildishServer
     */
    protected $db;

    /**
     * @var \Childish\query\Builder
     */
    protected $defaultDataBase;

    /**
     * CurdByDb constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        // 建立连接
        $capsule     = new ChildishServer;
        $connections = $this->getDefaultConfig();
        foreach ($connections as $name => $config) {
            $capsule->addConnection($config, $name);
            $capsule->getConnection($name)->enableQueryLog();
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->db = $capsule;
    }

    /**
     * 测试最终生成SQL
     *
     * @access public
     */
    public function tearDown()
    {
        $sql = DbModelTest::getTableConnection()->getQueryLog();
        var_dump($sql);
    }


    /**
     * 测试新增内容入库
     *
     * @test
     */
    public function testAdd()
    {
        $result = DbModelTest::insertCasually(array ('phone' => '131313'));
        echo $result;
        $this->assertGreaterThan(0, $result);
    }

    /**
     * 测试更新内容
     *
     * @test
     */
    public function testUpdate()
    {
        $result = DbModelTest::query()->where('phone', '131313')->update(array ('name' => 'vv'));
        echo $result;
        $this->assertGreaterThan(0, $result);
    }

    /**
     *  测试查询内容
     *
     * @test
     */
    public function testSelect()
    {
        $result = DbModelTest::query()->where('phone', "131313")->get(array ('phone'));
        var_dump($result);

        $this->assertInstanceOf('\Childish\support\Collection', $result);
    }

    /**
     * 测试删除内容
     *
     * @test
     */
    public function testDelete()
    {
        $result = DbModelTest::query()->where(array ('phone' => '131313'))->delete();
        $this->assertGreaterThan(0, $result);
    }
}

/**
 * DbModelTest
 *
 * @package  Tests\functional
 * @uses     description
 * @version  1.0
 * @author   Pu ShaoWei <pushaowei520@gamil.com>
 */
class DbModelTest extends ChildishModel
{
    /**
     * @var string smart 数据库
     */
    protected $connection = 'smart';
    /**
     * @var string table
     */
    protected $table = 'test';

    /**
     * @var string 更新时间
     */
    const UPDATED_AT = 'update_time';
    /**
     * @var string 创建时间
     */
    const CREATED_AT = 'create_time';


    /**
     * 模型内插入测试
     *
     * @static
     * @param array $input
     * @return int
     */
    public static function insertCasually(array $input)
    {
        return self::query()->insertGetId($input);
    }
}