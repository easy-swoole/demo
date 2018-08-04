<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午3:13
 */

namespace App\HttpController;


use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\MysqlPoolObj;
use EasySwoole\Component\Pool\PoolManager;

class Index extends Base
{
    function index()
    {
        $this->response()->write('this is hello world');
    }

    function test()
    {
        $obj = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj(0.1);
        if($obj instanceof MysqlPoolObj){
            try{
                $res = $obj->get('user_list');
                var_dump(count($res));
            }catch (\Throwable $throwable){

            }finally{
                PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($obj);
            }
        }

    }
}