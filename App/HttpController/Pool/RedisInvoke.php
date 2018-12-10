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
use EasySwoole\Component\Pool\Exception\PoolEmpty;
use EasySwoole\Component\Pool\Exception\PoolUnRegister;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Http\Message\Status;

class RedisInvoke extends Base
{
    function index() {
        try {
            $result = PoolManager::getInstance()->getPool(RedisPool::class)
                ->invoke(function(RedisObject $redis) {
                    $name = $redis->get('name');
                    return $name;
                });
            $this->writeJson(Status::CODE_OK, $result);
        } catch (\Throwable $throwable) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $throwable->getMessage());
        } catch (PoolEmpty $poolEmpty) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $poolEmpty->getMessage());
        } catch (PoolUnRegister $poolUnRegister) {
            $this->writeJson(Status::CODE_BAD_REQUEST, null, $poolUnRegister->getMessage());
        }
    }
}