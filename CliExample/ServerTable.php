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
use Swoole\Table;

$tableName = 'SERVICE_MANAGER';
$config = new Config();
//注册服务名称
$config->setServiceName('ser1');
$config->setExtra(['tableName' => $tableName]);
//设置服务的Ip(ps:集群)
//$config->setServiceIp('127.0.0.1');
//设置版本
//$config->setServiceVersion('1.0.1');
//设置广播地址，可以多个地址
//$config->getAutoFindConfig()->setAutoFindBroadcastAddress(['127.0.0.1:9600']);
//设置广播监听地址
//$config->getAutoFindConfig()->setAutoFindListenAddress('127.0.0.1:9600');
//设置广播秘钥
//$config->getAutoFindConfig()->setEncryptKey('123456abcd');
//设置节点管理器
$config->setNodeManager(\EasySwoole\Rpc\NodeManager\TableManager::class);
//设置接收数据格式
//$config->setSerializeType($config::SERIALIZE_TYPE_JSON);

$rpc = new Rpc($config);
//注册方法
$rpc->registerAction('call1', function (Request $request, Response $response) {
    //获取请求参数
    var_dump($request->getArg());
    //设置返回给客户端信息
    $response->setMessage('response');
});
$rpc->registerAction('call2', function (Request $request, Response $response) {
});


$http = new swoole_http_server("0.0.0.0", 9525);
//添加自定义进程（监听和广播）
$http->addProcess($rpc->autoFindProcess('es_rpc_process_1')->getProcess());

//rpc作为一个子服务运行
$sub = $http->addlistener("127.0.0.1", 9526, SWOOLE_TCP);

$rpc->attachToServer($sub);
/**
 * 再定义一个服务
 */
$configTwo = new Config();
$configTwo->setExtra(['tableName' => $tableName]);
$configTwo->setServiceName('ser2');

$configTwo->setNodeManager(\EasySwoole\Rpc\NodeManager\TableManager::class);

$rpcTwo = new Rpc($configTwo);

$rpcTwo->registerAction('call1', function (Request $request, Response $response) {
    $response->setMessage('this is ser2 action call1');
});

$http->addProcess($rpcTwo->autoFindProcess('es_rpc_process_2')->getProcess());
//rpc作为一个子服务运行
$subTwo = $http->addlistener("127.0.0.1", 9527, SWOOLE_TCP);

$rpcTwo->attachToServer($subTwo);

\EasySwoole\Component\TableManager::getInstance()->add($tableName, [
    'serviceIp' => ['type' => Table::TYPE_STRING, 'size' => 15],
    'servicePort' => ['type' => Table::TYPE_INT, 'size' => 4],
    'serviceVersion' => ['type' => Table::TYPE_STRING, 'size' => 8],
    'serviceName' => ['type' => Table::TYPE_STRING, 'size' => 32],
    'nodeExpire' => ['type' => Table::TYPE_INT, 'size' => 4],
    'nodeId' => ['type' => Table::TYPE_STRING, 'size' => 16],
], 4096);
/**
 * http请求回调
 */
$http->on("request", function ($request, $response) use ($config, $configTwo) {
//    $list = $config->getNodeManager()->allServiceNodes();
//    $list=$config->getNodeManager()->getServiceNodes('ser1');
    $s1 = $config->getNodeManager()->getServiceNode($config->getServiceName());
    $s2 = $config->getNodeManager()->getServiceNode($configTwo->getServiceName());
    $response->end(json_encode([$s1->toArray(), $s2->toArray()]));
//    $response->end(json_encode($list));
//    $response->end("Hello World\n");
});


$http->start();


////rpc 作为主服务运行
//$tcp = new swoole_server('127.0.0.1', 9526);
//$tcp->addProcess($autoFindProcess->getProcess());
//$rpc->attachToServer($tcp);

//$tcp->start();