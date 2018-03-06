<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午9:50
 */

namespace App\HttpController\Error;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        //error  并不会被响应到客户端中。
        echo $a;
        $this->response()->write('error index');
    }

    function fatal()
    {
        //未重构本控制器异常处理的时候
        $test = new XXXXXXX();
        $this->response()->write('error fatal');
    }
}