<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 18:59
 */

namespace App\Utility\Pool;

use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\EasySwoole\Config;

class RedisPool extends AbstractPool
{
    protected function createObject()
    {
        $redis = new RedisPoolObject;
        $conf = Config::getInstance()->getConf('REDIS');
        $redis->connect($conf['HOST'], $conf['PORT']);
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        if (!empty($conf['AUTH'])) {
            $redis->auth($conf['AUTH']);
        }
        return $redis;
    }
}