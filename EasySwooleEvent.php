<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Rpc\RpcServer;
use App\Rpc\RpcTwo;
use App\Rpc\ServiceOne;
use App\Utility\Pool\MysqlPool;
use App\Utility\TrackerManager;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Trace\Bean\Tracker;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        /*
           * ***************** 协程数据库连接池 ********************
         */

        PoolManager::getInstance()->register(MysqlPool::class);

        //调用链追踪器设置Token获取值为协程id
        TrackerManager::getInstance()->setTokenGenerator(function (){
            return \Swoole\Coroutine::getuid();
        });
        //每个链结束的时候，都会执行的回调
        TrackerManager::getInstance()->setEndTrackerHook(function ($token,Tracker $tracker){
            Logger::getInstance()->console((string)$tracker);
        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
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
        RpcServer::getInstance($conf,Trigger::getInstance());
        try{
            RpcServer::getInstance()->registerService('serviceOne',ServiceOne::class);
            RpcServer::getInstance()->registerService('serviceTwo',RpcTwo::class);
            RpcServer::getInstance()->attach(ServerManager::getInstance()->getSwooleServer());
        }catch (\Throwable $throwable){
            Logger::getInstance()->console($throwable->getMessage());
        }
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        //为每个请求做标记
        TrackerManager::getInstance()->getTracker()->addAttribute('workerId',ServerManager::getInstance()->getSwooleServer()->worker_id);
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
        //因为在onRequest 中部分代码有埋点，比如UserModelOne，会产生追踪链，因此需要清理。
        TrackerManager::getInstance()->closeTracker();
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data):void
    {

    }

}