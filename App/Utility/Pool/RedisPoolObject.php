<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 18:59
 */

namespace App\Utility\Pool;

use EasySwoole\Component\Pool\PoolObjectInterface;

class RedisPoolObject extends \Redis implements PoolObjectInterface
{
    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function beforeUse(): bool
    {
        return true;
    }
}