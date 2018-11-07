<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/7 0007
 * Time: 16:24
 */

namespace App\HttpController;


use EasySwoole\EasySwoole\Console\TcpService;
use EasySwoole\Http\AbstractInterface\Controller;

class Console extends Controller
{
    function index()
    {
        $this->response()->write("Console");
        if (\EasySwoole\EasySwoole\Config::getInstance()->getDynamicConf('CONSOLE.PUSH_LOG')) {
            TcpService::push('主动推送给控制台');
        }

        // TODO: Implement index() method.
    }

}