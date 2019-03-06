<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/26
 * Time: 12:31 PM
 */

namespace App\Model;


use App\Utility\Pool\Mysql2Object;
use App\Utility\Pool\Mysql2Pool;
use App\Utility\Pool\MysqlObject;
use EasySwoole\Component\Pool\PoolManager;

/**
 * model写法2,通过构造函数和析构函数去获取/回收连接
 * Class BaseModel
 * @package App\Model
 */
class BaseModel4
{
    private $db;

    function __construct()
    {
        $this->db = PoolManager::getInstance()->getPool(Mysql2Pool::class)->getObj();
    }

    protected function getDb():Mysql2Object
    {
        return $this->db;
    }

    function getDbConnection():Mysql2Object
    {
        return $this->db;
    }

    public function __destruct()
    {
        PoolManager::getInstance()->getPool(Mysql2Pool::class)->recycleObj($this->getDb());
        // TODO: Implement __destruct() method.
    }


}