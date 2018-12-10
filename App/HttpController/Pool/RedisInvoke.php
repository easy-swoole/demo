<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-12-10
 * Time: 上午9:47
 */

namespace App\HttpController\Pool;


use App\HttpController\Base;
use App\Utility\Pool\RedisObject;
use App\Utility\Pool\RedisPool;
use EasySwoole\Http\Message\Status;

class RedisInvoke extends Base
{
    function index() {
        try {
            $result = RedisPool::invoke(function(RedisObject $redis) {
                    $name = $redis->get('name');
                    return $name;
                });
            $this->writeJson(Status::CODE_OK, $result);
        } catch (\Throwable $throwable) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $throwable->getMessage());
        }
    }
}