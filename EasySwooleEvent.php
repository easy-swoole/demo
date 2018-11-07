<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Crontab\TaskOne;
use App\Crontab\TaskTwo;
use App\Process\ProcessTest;
use App\Rpc\RpcServer;
use App\Rpc\RpcTwo;
use App\Rpc\ServiceOne;
use App\Task\TaskTest;
use App\Utility\ConsoleCommand\TrackerLogCategory;
use App\Utility\ConsoleCommand\TrackerPushLog;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use App\Utility\TrackerManager;
use App\WebSocket\WebSocketEvent;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\Di;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Console\CommandContainer;
use EasySwoole\EasySwoole\Console\TcpService;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\EasySwoole\Swoole\Time\Timer;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Client\Tcp;
use EasySwoole\Socket\Dispatcher;
use EasySwoole\Trace\Bean\Tracker;
use EasySwoole\Utility\File;
use Swoole\Process;
use Swoole\Server;

class EasySwooleEvent implements Event
{
    /**
     * 框架初始化事件
     * 在Swoole没有启动之前 会先执行这里的代码
     */
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');//设置时区
        $tempDir = EASYSWOOLE_ROOT . '/Temp2';
        Config::getInstance()->setConf('TEMP_DIR', $tempDir);//重新设置temp文件夹
        Di::getInstance()->set(SysConst::SHUTDOWN_FUNCTION, function () {//注册自定义代码终止回调
            $error = error_get_last();
            if (!empty($error)) {
                var_dump($error);
            }
        });

        //调用链追踪器设置Token获取值为协程id
        TrackerManager::getInstance()->setTokenGenerator(function () {
            return \Swoole\Coroutine::getuid();
        });
        //每个链结束的时候，都会执行的回调
        TrackerManager::getInstance()->setEndTrackerHook(function ($token, Tracker $tracker) {
//            Logger::getInstance()->console((string)$tracker);
            //这里请读取动态配置 TrackerPushLog 来判断是否推送，读取TrackerLogCategory 判断推送分类
            $trackerPushLogStatus = Config::getInstance()->getDynamicConf('CONSOLE.TRACKER_PUSH_LOG');
            if ($trackerPushLogStatus) {
                $trackerLogCategory = Config::getInstance()->getDynamicConf('CONSOLE.TRACKER_LOG_CATEGORY');
                if ($trackerLogCategory){
                    if (in_array('all',$trackerLogCategory)){
                        TcpService::push((string)$tracker);
                    }else{
                        TcpService::push($tracker->toString($trackerLogCategory));
                    }
                }
            }
        });

        // 设置Tracker的推送配置和命令，以下配置请写入动态配置项
        CommandContainer::getInstance()->set('trackerPushLog',new TrackerPushLog());
        CommandContainer::getInstance()->set('trackerLogCategory',new TrackerLogCategory());
        //默认开启，推送全部日志
        Config::getInstance()->setDynamicConf('CONSOLE.TRACKER_LOG_CATEGORY',['all']);
        Config::getInstance()->setDynamicConf('CONSOLE.TRACKER_PUSH_LOG',true);


        //引用自定义文件配置
        self::loadConf();
        Config::getInstance()->setDynamicConf('test_config_value', 0);//配置一个动态配置项
        Config::getInstance()->setConf('test_config_value', 0);//配置一个普通配置项

        // 注册mysql数据库连接池
        PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));

        // 注册redis连接池
        PoolManager::getInstance()->register(RedisPool::class, Config::getInstance()->getConf('REDIS.POOL_MAX_NUM'));

    }

    public static function mainServerCreate(EventRegister $register)
    {
        //注册onWorkerStart回调事件
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            // var_dump('worker:' . $workerId . 'start');
        });
        //注册自定义进程
        ServerManager::getInstance()->getSwooleServer()->addProcess((new ProcessTest('test_process'))->getProcess());

        //添加子服务监听
        $subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0', 9502, SWOOLE_TCP);
        $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "subport on receive \n";
        });
        $subPort->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "subport on connect \n";
        });

        //主swoole服务修改配置
//        ServerManager::getInstance()->getSwooleServer()->set(['worker_num' => 1, 'task_worker_num' => 1]);

        /*
         * ***************** RPC ********************
        */
        $conf = new \EasySwoole\Rpc\Config();
        $conf->setSubServerMode(true);//设置为子务模式
        /*
         * 开启服务自动广播，可以修改广播地址，实现定向ip组广播
         */
        $conf->setEnableBroadcast(true);
        $conf->getBroadcastList()->set([
            '255.255.255.255:9602'
        ]);

        /*
         * 注册配置项和服务注册
         */
        RpcServer::getInstance($conf, Trigger::getInstance());
        try {
            RpcServer::getInstance()->registerService('serviceOne', ServiceOne::class);
            RpcServer::getInstance()->registerService('serviceTwo', RpcTwo::class);
            RpcServer::getInstance()->attach(ServerManager::getInstance()->getSwooleServer());
        } catch (\Throwable $throwable) {
            Logger::getInstance()->console($throwable->getMessage());
        }
        /**
         * **************** tcp控制器 **********************
         */
        $server = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addListener('0.0.0.0', 9503, SWOOLE_TCP);
        $subPort->set(
            ['open_length_check' => false]//不验证数据包
        );
        $socketConfig = new \EasySwoole\Socket\Config();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new \App\TcpController\Parser());
        //设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function (Server $server, $throwable, $raw, Tcp $client, $response) {
            $server->send($client->getFd(), 'bye');
            $server->close($client->getFd());
        });
        $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);
        $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });

        /**
         * **************** websocket控制器 **********************
         */
        // 创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType($conf::WEB_SOCKET);
        // 设置解析器对象
        $conf->setParser(new WebSocketParser());
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);
        // 给server 注册相关事件 在 WebSocket 模式下  message 事件必须注册 并且交给 Dispatcher 对象处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
        //自定义握手
        $websocketEvent = new WebSocketEvent();
        $register->set(EventRegister::onHandShake, function (\swoole_http_request $request, \swoole_http_response $response) use ($websocketEvent) {
            $websocketEvent->onHandShake($request, $response);
        });


        /**
         * **************** udp服务 **********************
         */

        //新增一个udp服务
        $server = ServerManager::getInstance()->getSwooleServer();
        $subPort = $server->addListener('0.0.0.0', '9605', SWOOLE_UDP);
        $subPort->on('packet', function (\swoole_server $server, string $data, array $client_info) {
            echo "udp packet:{$data}";
        });
        //udp客户端
        //添加自定义进程做定时udp发送
        $server->addProcess(new \swoole_process(function (\swoole_process $process) {
            //服务正常关闭
            $process::signal(SIGTERM, function () use ($process) {
                $process->exit(0);
            });
            //每隔5秒发送一次数据
            \Swoole\Timer::tick(5000, function () {
                if ($sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                    socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, true);
                    $msg = '123456';
                    socket_sendto($sock, $msg, strlen($msg), 0, '255.255.255.255', 9605);//广播地址
                    socket_close($sock);
                }
            });
        }));


        /**
         * **************** 异步客户端 **********************
         */
        //纯原生异步
        ServerManager::getInstance()->getSwooleServer()->addProcess(new Process(function (){
            $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
            $client->on("connect", function(\swoole_client $cli) {
                $cli->send("test:delay");
            });
            $client->on("receive", function(\swoole_client $cli, $data){
                echo "Receive: $data";
                $cli->send("test:delay");
                sleep(1);
            });
            $client->on("error", function(\swoole_client $cli){
                echo "error\n";
            });
            $client->on("close", function(\swoole_client $cli){
                echo "Connection close\n";
            });
            $client->connect('192.168.159.1', 9502);


            //本demo自定义进程采用的是原生写法,如果需要使用,请使用自定义进程类模板开发
            if (extension_loaded('pcntl')) {//异步信号,使用自定义进程类模板不需要该代码
                pcntl_async_signals(true);
            }
            Process::signal(SIGTERM,function (){//信号回调,使用自定义进程类模板不需要该代码
                $this->swooleProcess->exit(0);
            });
        }));

        /**
         * **************** Crontab任务计划 **********************
         */
        // 开始一个定时任务计划
        Crontab::getInstance()->addTask(TaskOne::class);
        // 开始一个定时任务计划
        Crontab::getInstance()->addTask(TaskTwo::class);

        // TODO: Implement mainServerCreate() method.
    }


    /**
     * 引用自定义配置文件
     * @throws \Exception
     */
    public static function loadConf()
    {
        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App/Config');
        if (is_array($files)) {
            foreach ($files['files'] as $file) {
                $fileNameArr = explode('.', $file);
                $fileSuffix = end($fileNameArr);
                if ($fileSuffix == 'php') {
                    Config::getInstance()->loadFile($file);
                } elseif ($fileSuffix == 'env') {
                    Config::getInstance()->loadEnv($file);
                }
            }
        }
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        //为每个请求做标记
        TrackerManager::getInstance()->getTracker()->addAttribute('workerId', ServerManager::getInstance()->getSwooleServer()->worker_id);
        if ((0/*auth fail伪代码,拦截该请求,判断是否有效*/)) {
            $response->end(true);
            return false;
        }
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
        //tracker结束
        TrackerManager::getInstance()->closeTracker();
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data): void
    {
        echo "TCP onReceive.\n";

    }

}