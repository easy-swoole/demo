<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/6 0006
 * Time: 14:46
 */

namespace App\HttpController;

use App\Model\ConditionBean;
use App\Model\Member\Member4Model;
use App\Model\Member\MemberModel;
use App\Utility\Pool\RedisObject;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Spl\SplBean;

/**
 * Redis使用实例
 * Class Index2
 * @package App\HttpController
 */
class Redis extends BaseWithDb
{
    function index()
    {
        RedisPool::invoke(function (RedisObject $redis){
            $redis->set('key','仙士可');
            $data = $redis->get('key');
            $this->response()->write($data);
        });
    }
}