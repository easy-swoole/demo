<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Utility\Pool\RedisPool;
use App\WebSocket\WebSocketEvents;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * mainServerCreate
     * @param EventRegister $register
     * @throws \Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {
        PoolManager::getInstance()->register(RedisPool::class, 20);

        // 注册WS事件回调
        $conf = new \EasySwoole\Socket\Config();
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser);
        $dispatch = new Dispatcher($conf);

        // 收到客户端消息时的处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });

        // 链接打开和关闭时的处理
        $register->set(EventRegister::onOpen, [WebSocketEvents::class, 'onOpen']);
        $register->set(EventRegister::onClose, [WebSocketEvents::class, 'onClose']);

        // 启动时清理 在线用户列表直接清空
        $register->add($register::onWorkerStart, function (\swoole_websocket_server $server, $workerId) {
            if ($workerId == 0) {
                WebSocketEvents::cleanOnlineUser();
            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data): void
    {

    }

}