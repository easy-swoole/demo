<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午1:20
 */

namespace App\HttpController;

use App\Rpc\RpcServer;
use EasySwoole\Component\Context;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Bean\Response;

class MysqlContext extends Controller
{
    function index()
    {
        $mysql_object = Context::getInstance()->get('Mysql');
        $data = $mysql_object->rawQuery("select 1;");
        var_dump($data);
    }
}