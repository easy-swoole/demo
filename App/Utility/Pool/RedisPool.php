<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:31
 */

namespace App\Utility\Pool;


use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\EasySwoole\Config;

class RedisPool extends AbstractPool
{

    protected function createObject()
    {
        // TODO: Implement createObject() method.
        $config = Config::getInstance()->getConf('REDIS');
        $redis = new RedisObject();
        if ($redis->connect($config['host'], $config['port'])) {
            if (!empty($config['auth'])) {
                $redis->auth($config['auth']);
            }
            return $redis;
        } else {
            return null;
        }
    }
}