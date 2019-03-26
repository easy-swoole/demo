<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/2/27
 * Time: 10:00
 */
include_once dirname(__DIR__) . "/vendor/autoload.php";

use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Request;
use EasySwoole\Rpc\Response;

$config = new Config();
//注册服务名称
$config->setServiceName('ser1');
//设置广播地址，可以多个地址
$config->getAutoFindConfig()->setAutoFindBroadcastAddress(['127.0.0.1:9600']);
//设置广播监听地址
$config->getAutoFindConfig()->setAutoFindListenAddress('127.0.0.1:9600');
//$config->setNodeManager(\EasySwoole\Rpc\NodeManager\TableManager::class);//设置节点管理器处理类,默认是EasySwoole\Rpc\NodeManager\FileManager


$rpc = new Rpc($config);
//注册响应方法
$rpc->registerAction('call1', function (Request $request, Response $response) {
    //获取请求参数
    var_dump($request->getArg());
    //设置返回给客户端信息
    $response->setMessage('response');
});
//注册响应方法2
$rpc->registerAction('call2', function (Request $request, Response $response)
{});


//监听/广播 rpc 自定义进程对象
$autoFindProcess = $rpc->autoFindProcess('es_rpc_process_1');



//创建第二个rpc服务
$config2=new Config();
$config2->setServiceName('ser2');
$rpc2 = new Rpc($config2);

//监听/广播 rpc 自定义进程对象
$autoFindProcess2 = $rpc2->autoFindProcess('es_rpc_process_2');

//创建http swoole服务
$http = new swoole_http_server("127.0.0.1", 9525);

//添加自定义进程到服务,开启进程
$http->addProcess($autoFindProcess->getProcess());
$http->addProcess($autoFindProcess2->getProcess());

//rpc作为一个子服务运行
$sub = $http->addlistener("127.0.0.1", 9527, SWOOLE_TCP);
$sub2 = $http->addlistener("127.0.0.1", 9528, SWOOLE_TCP);

//将swoole tcp子服务注入到rpc对象中,开始监听处理
$rpc->attachToServer($sub);
$rpc2->attachToServer($sub2);

/**
 * http请求回调
 */
$http->on("request", function ($request, $response) {
    $response->end("Hello World\n");
});
$http->start();


////rpc 作为主服务运行
//$tcp = new swoole_server('127.0.0.1', 9526);
//$tcp->addProcess($autoFindProcess->getProcess());
//$rpc->attachToServer($tcp);

//$tcp->start();