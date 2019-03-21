<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Store\AuthToken;
use App\Store\OnlineUser;
use App\WebSocket\WebSocketEvent;
use App\WebSocket\WebSocketParser;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        AuthToken::getInstance();   // 创建扫码令牌表
        OnlineUser::getInstance();  // 创建在线用户表

        // 初始化分发器
        $conf = new \EasySwoole\Socket\Config();
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser);
        $dispatch = new Dispatcher($conf);

        // 注册WebSocket事件
        $register->add(EventRegister::onOpen, [WebSocketEvent::class, 'onOpen']);
        $register->add(EventRegister::onClose, [WebSocketEvent::class, 'onClose']);
        $register->add(EventRegister::onMessage, function (\swoole_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame);
        });

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }
}