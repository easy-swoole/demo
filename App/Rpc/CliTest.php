<?php
include 'vendor/autoload.php';
$conf = new \EasySwoole\Rpc\Config();
//开启通讯密钥
//$conf->setAuthKey('123456');
$rpc = new \EasySwoole\Rpc\Rpc($conf);

//虚拟一个服务节点
$serviceNode = new \EasySwoole\Rpc\ServiceNode();
$serviceNode->setServiceName('serviceName');
$serviceNode->setServiceIp('127.0.0.1');
$serviceNode->setServicePort(9601);
$serviceNode->setNodeId('qwe');
//设置为永不过期
$serviceNode->setNodeExpire(0);
$rpc->nodeManager()->refreshServiceNode($serviceNode);

go(function () use ($rpc) {
    $client = $rpc->client();
    $client->selectService('serviceOne')->callAction('a1')->setArg(
        [
            'callTime' => time()
        ]
    )->onSuccess(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
        var_dump('success' . $response->getMessage());
    })->onFail(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
        var_dump('fail' . $response->getStatus());
    })->setTimeout(1.5);

    $client->selectService('serviceName')->callAction('a2')->onSuccess(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
        var_dump('success' . $response->getMessage());
    });
    $client->call(1.5);
});