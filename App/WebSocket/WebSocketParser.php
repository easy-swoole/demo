<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 19:08
 */

namespace App\WebSocket;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class WebSocketParser implements ParserInterface
{
    /**
     * 解码上来的消息
     * @param string $raw 消息内容
     * @param WebSocketClient $client 当前的客户端
     * @return Caller|null
     */
    public function decode($raw, $client): ?Caller
    {
        $caller = new Caller;
        // 聊天消息 {"controller":"broadcast","action":"roomBroadcast","params":{"content":"111"}}
        if ($raw !== 'PING') {
            $payload = json_decode($raw, true);
            $class = isset($payload['controller']) ? $payload['controller'] : 'index';
            $action = isset($payload['action']) ? $payload['action'] : 'actionNotFound';
            $params = isset($payload['params']) ? (array)$payload['params'] : [];
            $controllerClass = "\\App\\WebSocket\\Controller\\" . ucfirst($class);
            if (!class_exists($controllerClass)) $controllerClass = "\\App\\WebSocket\\Controller\\Index";
            $caller->setClient($caller);
            $caller->setControllerClass($controllerClass);
            $caller->setAction($action);
            $caller->setArgs($params);
        } else {
            $caller->setControllerClass("\\App\\WebSocket\\Controller\\Index");
            $caller->setAction('heartbeat');
        }
        return $caller;
    }

    /**
     * 打包下发的消息
     * @param Response $response 控制器返回的响应
     * @param WebSocketClient $client 当前的客户端
     * @return string|null
     */
    public function encode(Response $response, $client): ?string
    {
        return $response->getMessage();
    }
}