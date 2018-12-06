<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 上午11:55
 */

namespace App\HttpController;

use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Config;

/**
 * 带数据库链接的控制器基类
 * Class BaseWithDb
 * @package App\HttpController
 */
abstract class BaseWithDb extends Base
{
    protected $db;

    /**
     * 请求到来时 获取一个连接
     * @param string|null $action
     * @return bool|null
     * @throws \Exception
     */
    function onRequest(?string $action): ?bool
    {
        $timeout = Config::getInstance()->getConf('MYSQL.POOL_TIME_OUT');
        $mysqlObject = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj($timeout);

        // 请注意判断类型 避免拿到非期望的对象产生误操作
        if ($mysqlObject instanceof MysqlObject) {
            $this->db = $mysqlObject;
        } else {
            //直接抛给异常处理，不往下
            throw new \Exception('url :' . $this->request()->getUri()->getPath() . ' error,Mysql Pool is Empty');
        }

        // 不要忘记 call parent
        return parent::onRequest($action);
    }

    protected function gc()
    {
        // 请注意判断类型 避免将不属于该链接池的对象回收到池中
        if ($this->db instanceof MysqlObject) {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);

            // 请注意 此处db是该链接对象的引用 即使操作了回收 仍然能访问
            // 安全起见 请一定记得设置为null 避免再次被该控制器使用导致不可预知的问题
            $this->db = null;
        }
        parent::gc();
    }

    protected function getDbConnection(): MysqlObject
    {
        return $this->db;
    }

}