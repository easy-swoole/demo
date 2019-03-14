<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\console\TestConsole;
use App\Utility\Context\RegisterClassHandel;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Tests\ContextTest;
use EasySwoole\Console\ConsoleModuleContainer;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Rpc\NodeManager\FileManager;
use EasySwoole\Rpc\Rpc;
use PhpParser\Node\Expr\New_;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

    }

    public static function mainServerCreate(EventRegister $register)
    {
        #####################  rpc 服务1 #######################
        $rpcConfig = new \EasySwoole\Rpc\Config();
        //注册服务名称
        $rpcConfig->setServiceName('ser1');

//设置广播地址，可以多个地址
        $rpcConfig->getAutoFindConfig()->setAutoFindBroadcastAddress(['127.0.0.1:9600']);
//设置广播监听地址
        $rpcConfig->getAutoFindConfig()->setAutoFindListenAddress('127.0.0.1:9600');

        $rpcConfig->setNodeManager(FileManager::class);
        $rpc1 = new Rpc($rpcConfig);
        //注册响应方法
        $rpc1->registerAction('call1', function (\EasySwoole\Rpc\Request $request, \EasySwoole\Rpc\Response $response) {
            //获取请求参数
            var_dump($request->getArg());
            //设置返回给客户端信息
            $response->setMessage('response');
        });


        //监听/广播 rpc 自定义进程对象
        $autoFindProcess = $rpc1->autoFindProcess('es_rpc_process_1');
        //增加自定义进程去监听/广播服务
        ServerManager::getInstance()->getSwooleServer()->addProcess($autoFindProcess->getProcess());
        //起一个子服务去运行rpc
        ServerManager::getInstance()->addServer('rpc1',9527);
        $rpc1->attachToServer(ServerManager::getInstance()->getSwooleServer('rpc1'));
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}