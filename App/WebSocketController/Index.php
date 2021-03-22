<?php

namespace App\WebSocketController;

use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {
        // 获取 WebSocket 客户端调用的 controller 完整类名
        // string(29) "App\WebSocketController\Index"
        $controller = $this->caller()->getControllerClass();

        // 获取 WebSocket 客户端调用的 action 参数
        // string(5) "index"
        $action = $this->caller()->getAction();

        // 获取 WebSocket 客户端调用的 param 参数
        //
        $param = $this->caller()->getArgs();

        // 获取连接的 WebSocket Client 的相关信息
        $client = $this->caller()->getClient();

        var_dump($controller, $action, $param, $client);

        // 向 WebSocket 客户端响应 'this is index'
        $this->response()->setMessage('this is index');
    }
}