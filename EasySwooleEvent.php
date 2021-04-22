<?php

namespace EasySwoole\EasySwoole;

use App\Parser\WebSocketParser;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // 实现 Http 服务的 onRequest 事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response): bool {
            return true;
        });

        // 实现 Http 服务的 afterRequest 事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_AFTER_REQUEST, function (Request $request, Response $response): void {

        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
        ###### 处理 WebSocket 服务 ######
        $config = new \EasySwoole\Socket\Config();
        $config->setType($config::WEB_SOCKET);
        $config->setParser(WebSocketParser::class);
        $dispatcher = new \EasySwoole\Socket\Dispatcher($config);
        $config->setOnExceptionHandler(function (\Swoole\Server $server, \Throwable $throwable, string $raw, \EasySwoole\Socket\Client\WebSocket $client, \EasySwoole\Socket\Bean\Response $response) {
            $response->setMessage('system error!');
            $response->setStatus($response::STATUS_RESPONSE_AND_CLOSE);
        });

        // 自定义握手【设置 onHandShake 回调函数后不会再触发 onOpen 事件回调】
        /*$websocketEvent = new WebSocketEvent();
        $register->set(EventRegister::onHandShake, function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($websocketEvent) {
            $websocketEvent->onHandShake($request, $response);
        });*/

        // 处理 onOpen 回调
        $register->set($register::onOpen, function (\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) {
            var_dump($request->fd, $request->server);
            $server->push($request->fd, "hello, welcome\n");
        });

        // 处理 onMessage 回调
        $register->set($register::onMessage, function (\Swoole\Websocket\Server $server, \Swoole\Websocket\Frame $frame) use ($dispatcher) {
            $dispatcher->dispatch($server, $frame->data, $frame);
        });

        // 处理 onClose 回调
        $register->set($register::onClose, function ($ws, $fd) {
            if ($ws instanceof \Swoole\Http\Server) {
                return;
            }
            echo "client-{$fd} is closed\n";
        });

        ###### 处理 Http 服务 ######
        // 使用框架内部自带的 onRequest 回调，开发者只需要在 App\HttpController 控制器目录写对应的业务逻辑处理即可
    }
}
