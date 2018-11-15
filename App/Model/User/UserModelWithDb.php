<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-15
 * Time: 上午11:07
 */

namespace App\Model\User;


use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Config;

class UserModelWithDb
{
    protected $table = 'user';
    protected $db;

    function __construct()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj(Config::getInstance()->getConf('MYSQL.POOL_TIME_OUT'));
        if ($db instanceof MysqlObject) {
            $this->db = $db;
        } else {
            throw new \Exception('mysql pool is empty');
        }
    }

    private function getDb() {
        return $this->db;
    }

    /*
     * 获取列表数据
     */
    function getAll(int $page = 1, int $pageSize = 10) {
        $data = $this->getDb()->withTotalCount()->orderBy('id', 'DESC')->get($this->table, [($page - 1) * $pageSize, $page * $pageSize]);
        $total = $this->getDb()->getTotalCount();
        return ['data' => $data, 'total' => $total];
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if ($this->db instanceof MysqlObject) {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);
        }
    }

}