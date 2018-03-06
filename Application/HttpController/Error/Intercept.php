<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午9:55
 */

namespace App\HttpController\Error;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Intercept extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $test = new XXXXXXX();
        $this->response()->write('error fatal');
    }

    protected function onException(\Throwable $throwable, $actionName): void
    {
        //若重载实现了onException 方法，那么控制器内发生任何的异常，都会被该方法拦截，该方法决定了如何向客户端响应
        $this->response()->write($throwable->getMessage());
    }
}