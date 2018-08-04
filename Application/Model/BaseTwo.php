<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午10:35
 */

namespace App\Model;


use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\MysqlPoolObj;
use EasySwoole\Component\Pool\PoolManager;

class BaseTwo
{
    /*
     * 此种模式，在一个请求中，如果需要创建大量对象，且大量对象久久未释放的情况下，造成连接不够用。
     */
    private $db;
    function __construct()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        if($db instanceof MysqlPoolObj){
            $this->db = $db;
        }else{
            throw new \Exception('Db pool is empty');
        }
    }

    protected function getDb()
    {
        return $this->db;
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if($this->db instanceof MysqlPoolObj){
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);
        }
    }
}