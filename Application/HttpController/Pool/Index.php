<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/8
 * Time: 下午4:56
 */

namespace App\HttpController\Pool;


use App\Utility\MysqlPool;
use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        var_dump(time());
        \go(function () {
            $db = MysqlPool::getInstance()->getObj();
            if($db){
                $db->query('select sleep(1)');
                var_dump('exec query at '.time());
                MysqlPool::getInstance()->freeObj($db);
            }else{
                var_dump('mysql pool not available');
            }
        });
        \go(function () {
            $db = MysqlPool::getInstance()->getObj();
            if($db){
                $db->query('select sleep(1)');
                var_dump('exec query at '.time());
                MysqlPool::getInstance()->freeObj($db);
            }else{
                var_dump('mysql pool not available');
            }
        });
        var_dump(time());
        $this->response()->write('request over');
    }
}