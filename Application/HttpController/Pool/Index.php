<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/8
 * Time: 下午4:56
 */

namespace App\HttpController\Pool;


use App\Utility\MysqlPool;
use App\Utility\MysqlPool2;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        \go(function (){
            $db = MysqlPool::getInstance()->getObj();
            \go(function (){
                $db = MysqlPool::getInstance()->getObj();
                if($db){
                    $db->query('select sleep(1)');
                    MysqlPool::getInstance()->freeObj($db);
                }else{
                    var_dump('db not available');
                }
                var_dump('finish at'.time());
            });
            if($db){
                $db->query('select sleep(1)');
                MysqlPool::getInstance()->freeObj($db);
            }else{
                var_dump('db not available');
            }
            var_dump('finish at'.time());
        });

        $this->response()->write('request over');
    }

    function test()
    {
        \go(function (){
            $db = MysqlPool2::getInstance()->getObj();
            if($db){
                $ret = $db->where('account','%s%','LIKE')->get('user_list');
                MysqlPool2::getInstance()->freeObj($db);
                var_dump($ret);
            }else{
                var_dump('db not available');
            }
        });

        \go(function (){
            $db = MysqlPool2::getInstance()->getObj();
            if($db){
                $ret = $db->where('account','test2')->get('user_list');
                MysqlPool2::getInstance()->freeObj($db);
                var_dump($ret);
            }else{
                var_dump('db not available');
            }
        });


        $this->response()->write('request over');
    }


    function test2()
    {
        //协程同步调用（优化worker 利用时间，让一个worker可以同时处理多个用户请求）
        $ret = null;
        $db = MysqlPool2::getInstance()->getObj();

        if($db){
            var_dump($db->get('user_list'));
            MysqlPool2::getInstance()->freeObj($db);
        }else{
            var_dump('db not available');
        }

        $this->response()->write(json_encode($ret));
    }
}