<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Utility\Pool\Mysql2Pool;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Mysqli\Mysqli;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        //redis连接池注册(config默认为127.0.0.1,端口6379)
        \EasySwoole\RedisPool\Redis::getInstance()->register('redis',new \EasySwoole\Redis\Config\RedisConfig());

        //redis集群连接池注册
        \EasySwoole\RedisPool\Redis::getInstance()->register('redisCluster',new \EasySwoole\Redis\Config\RedisClusterConfig([
                ['172.16.253.156', 9001],
                ['172.16.253.156', 9002],
                ['172.16.253.156', 9003],
                ['172.16.253.156', 9004],
            ]
        ));

    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}