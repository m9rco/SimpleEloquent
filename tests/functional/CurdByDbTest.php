<?php
namespace Tests\functional;

use Tests\BaseTestCase;
use Childish\ChildishServer;

/**
 * Connection
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @version   1.0
 */
class CurdByDbTest extends BaseTestCase
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
        $this->db              = $capsule;
        $this->defaultDataBase = $this->db->getConnection('smart');
    }

    /**
     * 测试最终生成SQL
     *
     * @access public
     */
    public function tearDown()
    {
        $sql = $this->defaultDataBase->getQueryLog();
        var_dump($sql);
    }


    /**
     * 测试新增内容入库
     *
     * @test
     */
    public function testAdd()
    {
        $getId       = $this->defaultDataBase->table(self::DB_TEST_TABLE)->insert(array ('phone' => '12121212'));
        $insert      = $this->defaultDataBase->table(self::DB_TEST_TABLE)->insertGetId(array ('phone' => '12121212'));
        $batchInsert = $this->defaultDataBase->table(self::DB_TEST_TABLE)->insert(
            array (
                array (
                    'phone' => time(),
                    'name'  => $this->random()
                ),
                array (
                    'phone' => time(),
                    'name'  => $this->random()
                )
            )
        );
        // update or insert
        $judgeInsert = $this->defaultDataBase->table(self::DB_TEST_TABLE)->updateOrInsert(
            array ('phone' => '1212121'),
            array ('phone' => '1212121')
        );
        $this->assertTrue(true, (bool)$judgeInsert);
        $this->assertTrue(true, (bool)$getId);
        $this->assertTrue(true, (bool)$batchInsert);
        $this->assertGreaterThan(0, $insert);
    }

    /**
     * 测试查询内容
     *
     * @test
     */
    public function testSelect()
    {
        // 类似 all 以及 get 之类的可以取回多个结果的 Eloquent 方法，将会返回一个 Illuminate\Database\Eloquent\Collection 实例。
        // Collection 类提供 多种辅助函数 来处理你的 Eloquent 结果。
        /**
         * @reference https://d.laravel-china.org/docs/5.4/queries
         * @var  $getAll \Childish\support\Collection
         */
        // select `phone` from `smart_self::DB_TEST` where `phone` = ?
        $singeSelect = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where('phone',
            "1512992489")->get(array ('phone'));

        // select * from `smart_self::DB_TEST` where `phone` > ?  | >=  <> like
        $compositeSelect = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where('phone', '>', "1512992489")->get();

        // select * from `smart_self::DB_TEST` where `phone` like ?
        $likeSelect = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where('phone', 'like', "%15")->get();

        // select * from `smart_self::DB_TEST` where (`phone` = ? and `name` = ?)
        $conditionsSinge = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => "1512992489", 'name' => 'Jack'))->get();

        // select * from `smart_self::DB_TEST` where (`phone` > ? and `name` = ?)
        $judageSinge = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array (array ('phone', '>', "15129"), array ('name', 'Jack')))->get();

        // select distinct * from `smart_self::DB_TEST`
        $distinct = $this->defaultDataBase->table(self::DB_TEST_TABLE)->distinct()->get();

        // select * from `smart_self::DB_TEST`
        $getAll = $this->defaultDataBase->table(self::DB_TEST_TABLE)->get();

        // select count(*) as aggregate from `smart_self::DB_TEST`
        $count = $this->defaultDataBase->table(self::DB_TEST_TABLE)->count();

        // select max(`phone`) as aggregate from `smart_self::DB_TEST`
        $max = $this->defaultDataBase->table(self::DB_TEST_TABLE)->max('phone');

        // select
        $select = $this->defaultDataBase->select("SELECT * FROM smart_db_test");

        // select * from `smart_self::DB_TEST` where `phone` between ? and ?
        $between = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereBetween('phone', [1, 100])->get();

        // select * from `smart_self::DB_TEST` order by `id` desc limit 10
        $orderBy = $this->defaultDataBase->table(self::DB_TEST_TABLE)->orderBy('id', 'desc')->limit(10)->get();

        // select * from `smart_self::DB_TEST` limit 1
        $getRow = $this->defaultDataBase->table(self::DB_TEST_TABLE)->first();

        // select * from `smart_db_test` where `phone` is not null
        $notNull = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereNotNull('phone')->get();

        // select * from `smart_db_test` where `phone` is  null
        $null = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereNull('phone')->get();

        // select * from `smart_self::DB_TEST` where `phone` in (?, ?, ?)
        $getIn = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereIn('phone',
            array ('1212121', '21212'))->get();

        // select * from `smart_self::DB_TEST` where `phone` not in (?, ?, ?)
        $getNOTIn = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereNotIn('phone',
            array ('1212121', '2121'))->get();

        // select * from `smart_db_test` where date(`create_time`) = ?
        $date = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereDate('create_time', '2016-12-31')->get();

        // select * from `smart_db_test` where month(`create_time`) = ?
        $month = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereMonth('create_time', '12')->get();

        // select * from `smart_db_test` where day(`create_time`) = ?
        $day = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereDay('create_time', '31')->get();

        // select * from `smart_db_test` where year(`create_time`) = ?
        $year = $this->defaultDataBase->table(self::DB_TEST_TABLE)->whereYear('create_time', '2016')->get();

        // select * from `smart_db_test` group by `phone`
        $group = $this->defaultDataBase->table(self::DB_TEST_TABLE)->groupBy('phone')->get();

        // select * from `smart_db_test` group by `phone` having `phone` = ?
        $groupByHaving = $this->defaultDataBase->table(self::DB_TEST_TABLE)->groupBy('phone')->having('phone',
            100)->get();

        // select * from `smart_db_test` limit 5 offset 10
        $limitOffset = $this->defaultDataBase->table(self::DB_TEST_TABLE)->offset(10)->limit(5)->get();

        // select * from `smart_db_test` left join `smart_practice` on smart_db_test.id = smart_practice.test_id;
        $leftJoin = $this->defaultDataBase->table(self::DB_TEST_TABLE)
                                          ->leftJoin('practice', 'test.id', '=', 'practice.id')->get();

        // select * from `smart_db_test` right join `smart_practice` on smart_db_test.id = smart_practice.test_id;
        $rightJoin = $this->defaultDataBase->table(self::DB_TEST_TABLE)
                                           ->rightJoin('practice', 'test.id', '=', 'practice.id')->get();
        /**
         *  Advanced operation
         */
        // 你希望某个值为 true 时才执行查询。例如，如果在传入请求中存在指定的输入值的时候才执行这个 where 语句。你可以使用 when 方法实现：
        // 只有当 when 方法的第一个参数为 true 时，闭包里的 where 语句才会执行。如果第一个参数是 false，这个闭包将不会被执行。
        // select * from `smart_db_test` where `phone` = ?
        $phone = null;
        $this->defaultDataBase->table(self::DB_TEST_TABLE)->when($phone, function ($query) use ($phone) {
            return $query->where('phone', $phone);
        })->get();

        // select * from `smart_db_test` order by `name` asc
        // 你可能会把另一个闭包当作第三个参数传递给 when 方法。如果第一个参数的值为 false 时，这个闭包将执行。为了说明如何使用此功能，我们将使用它配置默认排序的查询：
        $sortBy   = null;
        $advanced = $this->defaultDataBase->table(self::DB_TEST_TABLE)->when($sortBy, function ($query) use ($sortBy) {
            return $query->orderBy($sortBy);
        }, function ($query) {
            return $query->orderBy('name');
        })->get();

        $this->assertInstanceOf('\Childish\support\Collection', $singeSelect);
        $this->assertInstanceOf('\Childish\support\Collection', $distinct);
        $this->assertInstanceOf('\Childish\support\Collection', $compositeSelect);
        $this->assertInstanceOf('\Childish\support\Collection', $conditionsSinge);
        $this->assertInstanceOf('\Childish\support\Collection', $getAll);
        $this->assertInstanceOf('\Childish\support\Collection', $getIn);
        $this->assertInstanceOf('\Childish\support\Collection', $getNOTIn);
        $this->assertInstanceOf('\stdClass', $getRow);
        $this->assertGreaterThan(0, $count);
        $this->assertGreaterThan(0, $max);
        $this->assertJson($getAll->toJson());
//        $this->assertArraySubset(json_decode($getAll->toJson(), true), $getAll->toArray()); // 是不相等的
    }

    /**
     * 测试更新内容
     *
     * @test
     */
    public function testUpdate()
    {
        // update `smart_db_test` set `phone` = `phone` + 1  where (`phone` = ?)
        $increment = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => '12121212'))->increment('phone');

        // update `smart_db_test` set `phone` = `phone` - 1  where (`phone` = ?)
        $decrement = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => '12121212'))->decrement('phone');

        // update `smart_db_test` set `phone` = ?  where (`phone` = ?)
        $update = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => '12121212'))->update(array ('phone' => 1111));

        $this->assertGreaterThan(0, $increment);
    }

    /**
     * testLock
     *
     * @test
     */
    public function testLock()
    {
        /**
         * 提供一处悲观锁请谨慎使用
         */
        // 若要在查询中使用「共享锁」，可以使用 sharedLock 方法。共享锁可防止选中的数据列被篡改，直到事务被提交为止
        // "select * from `smart_db_test` where (`phone` = ?) lock in share mode"
        $sharedLock = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => '12121212'))->sharedLock()->get();

        // 另外，你也可以使用 lockForUpdate 方法。使用「更新」锁可避免行被其它共享锁修改或选取：
        // select * from `smart_db_test` where (`phone` = ?) for update
        $forUpdate = $this->defaultDataBase->table(self::DB_TEST_TABLE)->where(array ('phone' => '12121212'))->lockForUpdate()->get();
        $this->assertInstanceOf('\Childish\support\Collection', $sharedLock);
        $this->assertInstanceOf('\Childish\support\Collection', $forUpdate);
    }

    /**
     * testTransaction
     *
     * @test
     */
    public function testTransaction()
    {
        // example A. autoload transaction
        try {
            $result = $this->defaultDataBase->transaction(function () {
                $this->defaultDataBase->table(self::DB_TEST_TABLE)->insert(array ('phone' => '18519866421'));
                $this->defaultDataBase->table(self::DB_PRACTICE)->updateOrInsert(
                    array ('content' => '18519866421'),
                    array ('content' => '18519866421')
                );
            });
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die;
        }

        // example B. manual transition
        $this->defaultDataBase->beginTransaction();
        try {
            $this->defaultDataBase->table(self::DB_TEST_TABLE)->insert(array ('phone' => '18519866429'));
            $this->defaultDataBase->table(self::DB_PRACTICE)->updateOrInsert(
                array ('content' => '18519866429'),
                array ('content' => '18519866429')
            );
            $this->defaultDataBase->commit();
        } catch (\Exception $e) {
            $this->defaultDataBase->rollBack();
            var_dump($e->getMessage());
            die;
        }
        $this->assertNull($result);
    }

    /**
     * 测试删除内容
     *
     * @test
     */
    public function testDelete()
    {
        /**
         * 请选择适合的技术选型，以下删除方法属于硬性删除，硬盘有价，数据无价
         */
        $delete = $this->defaultDataBase->table(self::DB_TEST_TABLE)
                                        ->where(array ('is_delete' => '0'))
                                        ->delete();
        // 如果你需要清空表，你可以使用 truncate 方法，这将删除所有行，并重置自动递增 ID 为零：
        $truncate = $this->defaultDataBase->table(self::DB_TEST_TABLE)
                                          ->truncate();
        $this->assertGreaterThan(0, $delete);
        $this->assertNull($truncate);
    }
}