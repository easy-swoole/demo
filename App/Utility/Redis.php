<?php

namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;

class Redis
{
    use Singleton;
    protected $redis;

    function __construct()
    {
        $this->redis = new \Redis();
        $this->connect();
    }

    function getConnect(): \Redis
    {
        return $this->redis;
    }

    function connect(): Redis
    {
        $conf = Config::getInstance()->getConf('REDIS');
        $this->redis->connect($conf['HOST'], $conf['PORT']);
        $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        if (!empty($conf['AUTH'])) {
            $this->redis->auth($conf['AUTH']);
        }
        return $this;
    }
}