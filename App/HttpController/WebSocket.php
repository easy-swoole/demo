<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\EasySwoole\ServerManager;

/**
 * Class WebSocket
 *
 * 此类是通过 http 请求来调用具体的事件
 * 实际生产中需要自行管理 fd -> user 的关系映射，这里不做详细解释
 *
 * @package App\HttpController
 */
class WebSocket extends Controller
{
    /**
     * 默认的 websocket 测试页
     */
    public function index()
    {
        $content = file_get_contents(__DIR__ . '/websocket.html');
        $this->response()->write($content);
        $this->response()->end();
    }

    /**
     * 使用HTTP触发广播给所有的WS客户端
     *
     * @example http://ip:9501/WebSocketTest/broadcast
     */
    public function broadcast()
    {
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();
        $start = 0;

        // 此处直接遍历所有FD进行消息投递
        // 生产环境请自行使用Redis记录当前在线的WebSocket客户端FD
        while (true) {
            $conn_list = $server->connection_list($start, 10);
            if (empty($conn_list)) {
                break;
            }
            $start = end($conn_list);
            foreach ($conn_list as $fd) {
                $info = $server->getClientInfo($fd);
                /** 判断此fd 是否是一个有效的 websocket 连接 */
                if ($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                    $server->push($fd, 'http broadcast fd ' . $fd . ' at ' . date('H:i:s'));
                }
            }
        }
    }

    /*
    * HTTP触发向某个客户端单独推送消息
    * @example http://ip:9501/WebSocketTest/push?fd=2
    */
    public function push()
    {
        $fd = $this->request()->getRequestParam('fd');
        if (is_numeric($fd)) {
            /** @var \swoole_websocket_server $server */
            $server = ServerManager::getInstance()->getSwooleServer();
            $info = $server->getClientInfo($fd);
            if ($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                $server->push($fd, 'http push to fd ' . $fd . ' at ' . date('H:i:s'));
            } else {
                $this->response()->write("fd {$fd} is not exist or closed");
            }
        } else {
            $this->response()->write("fd {$fd} is invalid");
        }
    }
}
