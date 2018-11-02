<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:33
 */

namespace App\Utility\Pool;


use EasySwoole\Component\Pool\PoolObjectInterface;
use Swoole\Coroutine\Redis;

class RedisObject extends Redis implements PoolObjectInterface
{

    function gc()
    {
        // TODO: Implement gc() method.
        // 回收
        $this->gc();
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
        return true;
    }
}