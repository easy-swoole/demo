<?php

namespace EasySwoole\EasySwoole;

use App\Parser\WebSocketParser;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Http\Dispatcher;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
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
            echo "client-{$fd} is closed\n";
        });



        ###### 处理 http 服务 ######
        $namespace = 'App\\HttpController\\';
        $depth = 5;
        $max = 500;
        $waitTime = 5;
        $dispatcher = Dispatcher::getInstance()->setNamespacePrefix($namespace)->setMaxDepth($depth)->setControllerMaxPoolNum($max)->setControllerPoolWaitTime($waitTime);;
        // 补充 HTTP_EXCEPTION_HANDLER 默认回调
        $httpExceptionHandler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
        if (!is_callable($httpExceptionHandler)) {
            $httpExceptionHandler = function ($throwable, $request, $response) {
                $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
                $response->write(nl2br($throwable->getMessage() . "\n" . $throwable->getTraceAsString()));
                Trigger::getInstance()->throwable($throwable);
            };
            Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, $httpExceptionHandler);
        }
        $dispatcher->setHttpExceptionHandler($httpExceptionHandler);

        // 配置 onRequest 事件
        $requestHook = function (Request $request, Response $response): bool {
            return true;
        };

        // 配置 afterRequest 事件
        $afterRequestHook = function (Request $request, Response $response): void {

        };

        // 处理 onRequest 回调
        $register->set($register::onRequest, function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($dispatcher, $requestHook, $afterRequestHook) {
            $request_psr = new Request($request);
            $response_psr = new Response($response);
            try {
                $ret = null;
                if (is_callable($requestHook)) {
                    $ret = call_user_func($requestHook, $request_psr, $response_psr);
                }
                if ($ret !== false) {
                    $dispatcher->dispatch($request_psr, $response_psr);
                }
            } catch (\Throwable $throwable) {
                call_user_func(Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER), $throwable, $request_psr, $response_psr);
            } finally {
                try {
                    if (is_callable($afterRequestHook)) {
                        call_user_func($afterRequestHook, $request_psr, $response_psr);
                    }
                } catch (\Throwable $throwable) {
                    call_user_func(Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER), $throwable, $request_psr, $response_psr);
                }
            }
            $response_psr->__response();
        });
    }
}
