<?php

namespace EasySwoole;

use App\Process\Inotify;
use App\Process\Test;
use App\Sock\Parser\Tcp;
use App\Sock\Parser\WebSock;
use App\Utility\MysqlPool2;
use \EasySwoole\Core\AbstractInterface\EventInterface;
use \EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Component\Pool\PoolManager;
use \EasySwoole\Core\Swoole\EventHelper;
use \EasySwoole\Core\Swoole\Process\ProcessManager;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Swoole\Task\TaskManager;
use \EasySwoole\Core\Swoole\Time\Timer;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;

/**
 * 全局事件定义文件
 * Class EasySwooleEvent
 * @package EasySwoole
 */
Class EasySwooleEvent implements EventInterface
{

    /**
     * 框架初始化事件
     * 在Swoole没有启动之前 会先执行这里的代码
     */
    public static function frameInitialize(): void
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * 创建主服务
     * 除了主服务之外还可以在这里创建额外的端口监听
     * @param ServerManager $server
     * @param EventRegister $register
     */
    public static function mainServerCreate(ServerManager $server, EventRegister $register): void
    {

        // 数据库协程连接池
        // @see https://www.easyswoole.com/Manual/2.x/Cn/_book/CoroutinePool/mysql_pool.html?h=pool
        // ------------------------------------------------------------------------------------------
        if (version_compare(phpversion('swoole'), '2.1.0', '>=')) {

            PoolManager::getInstance()->registerPool(MysqlPool2::class, 3, 10);
        }

        // 普通事件注册 swoole 中的各种事件都可以按这个例子来进行注册
        // @see https://www.easyswoole.com/Manual/2.x/Cn/_book/Core/event_register.html
        // ------------------------------------------------------------------------------------------
        $register->add($register::onWorkerStart, function (\swoole_server $server, $workerId) {
            //为第一个进程添加定时器
            if ($workerId == 0) {
                # 启动定时器
                Timer::loop(10000, function () {
                    Logger::getInstance()->console('timer run');  # 写日志到控制台
                    ProcessManager::getInstance()->writeByProcessName('test', time());  # 向自定义进程发消息
                });
            }
        });

        // 创建自定义进程 上面定时器中发送的消息 由 Test 类进行处理
        // @see https://www.easyswoole.com/Manual/2.x/Cn/_book/Advanced/process.html
        // ------------------------------------------------------------------------------------------
        ProcessManager::getInstance()->addProcess('test', Test::class);

        // 天天都在问的服务热重启 单独启动一个进程处理
        // ------------------------------------------------------------------------------------------
        ProcessManager::getInstance()->addProcess('autoReload', Inotify::class);

        // WebSocket 以控制器的方式处理业务逻辑
        // @see https://www.easyswoole.com/Manual/2.x/Cn/_book/Sock/websocket.html
        // ------------------------------------------------------------------------------------------
        EventHelper::registerDefaultOnMessage($register, WebSock::class);

        // 多端口混合监听
        // @see https://www.easyswoole.com/Manual/2.x/Cn/_book/Event/main_server_create.html
        // @see https://wiki.swoole.com/wiki/page/525.html
        // ------------------------------------------------------------------------------------------
        $tcp = $server->addServer('tcp', 9502);

        # 第二参数为TCP控制器 和WS一样 都可以使用控制器方式来解析收到的报文并处理
        # 第三参数为错误回调 可以不传入 当无法正确解析 或者是解析出来的控制器不在的时候会调用
        EventHelper::registerDefaultOnReceive($tcp, Tcp::class, function ($errorType, $clientData, \EasySwoole\Core\Socket\Client\Tcp $client) {
            TaskManager::async(function () use ($client) {
                sleep(3);
                \EasySwoole\Core\Socket\Response::response($client, "Bye");
                ServerManager::getInstance()->getServer()->close($client->getFd());
            });
            return "{$errorType} and going to close";
        });

        // 自定义WS握手处理 可以实现在握手的时候 鉴定用户身份
        // @see https://wiki.swoole.com/wiki/page/409.html
        // ------------------------------------------------------------------------------------------
        $register->add($register::onHandShake, function (\swoole_http_request $request, \swoole_http_response $response) {
            if (isset($request->cookie['token'])) {
                $token = $request->cookie['token'];
                if ($token == '123') {
                    // 如果取得 token 并且验证通过 则进入 ws rfc 规范中约定的验证过程
                    if (!isset($request->header['sec-websocket-key'])) {
                        // 需要 Sec-WebSocket-Key 如果没有拒绝握手
                        var_dump('shake fai1 3');
                        $response->end();
                        return false;
                    }
                    if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
                        || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
                    ) {
                        //不接受握手
                        var_dump('shake fai1 4');
                        $response->end();
                        return false;
                    }

                    $key     = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
                    $headers = array(
                        'Upgrade'               => 'websocket',
                        'Connection'            => 'Upgrade',
                        'Sec-WebSocket-Accept'  => $key,
                        'Sec-WebSocket-Version' => '13',
                        'KeepAlive'             => 'off',
                    );
                    foreach ($headers as $key => $val) {
                        $response->header($key, $val);
                    }
                    //接受握手  发送验证后的header   还需要101状态码以切换状态
                    $response->status(101);
                    var_dump('shake success at fd :' . $request->fd);
                    $response->end();
                } else {
                    // 令牌不正确的情况 不接受握手
                    var_dump('shake fail 2');
                    $response->end();
                }
            } else {
                // 没有携带令牌的情况 不接受握手
                var_dump('shake fai1 1');
                $response->end();
            }
        });
    }

    public static function onRequest(Request $request, Response $response): void
    {
        // 每个请求进来都先执行这个方法 可以作为权限验证 前置请求记录等
        $request->withAttribute('requestTime', microtime(true));
    }

    public static function afterAction(Request $request, Response $response): void
    {
        // 每个请求结束后都执行这个方法 可以作为后置日志等
        $start = $request->getAttribute('requestTime');
        $spend = round(microtime(true) - $start, 3);
        Logger::getInstance()->console("request :{$request->getUri()->getPath()} take {$spend}");
    }
}