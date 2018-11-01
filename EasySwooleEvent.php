<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Process\ProcessTest;
use App\Rpc\RpcServer;
use App\Rpc\RpcTwo;
use App\Rpc\ServiceOne;
use App\Utility\Pool\MysqlPool;
use App\Utility\TrackerManager;
use EasySwoole\Component\Di;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Client\Tcp;
use EasySwoole\Trace\Bean\Tracker;
use EasySwoole\Utility\File;
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

        //注册数据库协程连接池
        PoolManager::getInstance()->register(MysqlPool::class, 20);
        //调用链追踪器设置Token获取值为协程id
        TrackerManager::getInstance()->setTokenGenerator(function () {
            return \Swoole\Coroutine::getuid();
        });
        //每个链结束的时候，都会执行的回调
        TrackerManager::getInstance()->setEndTrackerHook(function ($token, Tracker $tracker) {
            Logger::getInstance()->console((string)$tracker);
        });

        //引用自定义文件配置
        self::loadConf();

    }

    public static function mainServerCreate(EventRegister $register)
    {
        //注册onWorkerStart回调事件
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            var_dump('worker:' . $workerId . 'start');
        });
        //注册自定义进程
        ServerManager::getInstance()->getSwooleServer()->addProcess((new ProcessTest('test_process'))->getProcess());

        //添加子服务监听
        $subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0', 9502, SWOOLE_TCP);
        $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "subport on receive \n";
        });

        //主swoole服务修改配置
        ServerManager::getInstance()->getSwooleServer()->set(['worker_num' => 1, 'task_worker_num' => 1]);

        /*TODO
          ****************** websocket ********************
        */

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
            ['open_length_check'   => false]//不验证数据包
        );
        $socketConfig = new \EasySwoole\Socket\Config();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new \App\TcpController\Parser());
        //设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function (Server $server,$throwable,$raw,Tcp $client,$response){
            $server->send($client->getFd(),'bye');
            $server->close($client->getFd());
        });
        $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);
        $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
            $dispatch->dispatch($server, $data, $fd, $reactor_id);
        });




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