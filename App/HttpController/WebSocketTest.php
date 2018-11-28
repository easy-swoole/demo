<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\EasySwoole\ServerManager;

class WebSocketTest extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $content = file_get_contents(__DIR__ . '/websocket.html');
        $this->response()->write($content);
    }

    /**
     * 使用HTTP触发广播给所有的WS客户端
     * @example http://ip:9501/WebSocketTest/broadcast
     */
    function broadcast()
    {
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();
        $start = 0;

        // 此处直接遍历所有FD进行消息投递
        // 生产环境请自行使用Redis记录当前在线的WebSocket客户端FD
        while (true) {
            $conn_list = $server->connection_list($start, 10);
            if ($conn_list === false or count($conn_list) === 0) {
                break;
            }
            $start = end($conn_list);
            foreach ($conn_list as $fd) {
                $info = $server->getClientInfo($fd);
                if ($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                    $server->push($fd, 'http broadcast fd ' . $fd . ' at ' . time());
                }
            }
        }
    }

    /*
    * HTTP触发向某个客户端单独推送消息
    * @example http://ip:9501/WebSocketTest/push?fd=2
    */
    function push()
    {
        $fd = $this->request()->getRequestParam('fd');
        if (is_numeric($fd)) {
            /** @var \swoole_websocket_server $server */
            $server = ServerManager::getInstance()->getSwooleServer();
            $info = $server->getClientInfo($fd);
            if ($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                $server->push($fd, 'http push to fd ' . $fd . ' at ' . time());
            } else {
                $this->response()->write("fd {$fd} is not exist or closed");
            }
        } else {
            $this->response()->write("fd {$fd} is invalid");
        }
    }
}
