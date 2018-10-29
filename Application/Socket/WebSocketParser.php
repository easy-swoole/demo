<?php
namespace App\Socket;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Client;
use EasySwoole\Socket\Bean\{
    Caller,
    Response
};

use App\Socket\Websocket\Test;

class WebSocketParser implements ParserInterface
{
    /**
     * decode
     * @param  string         $raw    客户端消息
     * @param  Client         $client Socket Client 对象
     * @return Caller         Socket 调用对象
     */
    public function decode($raw, $client) : ? Caller
    {
        // 开发者在这里将客户端发送来的消息解析成具体的调用控制器和方法
        // 开发者可以自己选择 event 模式 或者传统的控制器模式
        $jsonObject = json_decode($raw);

        // new 调用者对象
        $caller =  new Caller();
        // 设置被调用的类
        $class = '\\App\\Socket\\Websocket\\'. ucfirst($jsonObject->class ?? 'Test');
        $caller->setControllerClass($class);
        // 设置被调用的方法
        $caller->setAction($jsonObject->action);
        // 设置被调用的Args
        $caller->setArgs(isset($jsonObject->content) ? [$jsonObject->content] : []);
        return $caller;
    }

    /**
     * encode
     * @param  Response $response Socket Response 对象
     * @param  Client   $client   Socket Client 对象
     * @return string             发送给客户端的消息
     */
    public function encode(Response $response, $client) : ? string
    {
        // 这里返回响应给客户端的信息
        // 这里应当只做统一的encode操作 具体的状态等应当由 Controller处理
        return $response->getMessage();
    }
}
