<?php
/**
 * Created by PhpStorm.
 * User: xcg
 * Date: 2019/2/27
 * Time: 10:03
 */
include_once dirname(__DIR__) . "/vendor/autoload.php";

use EasySwoole\Rpc\Config;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Response;

$config = new Config();
//$config->setNodeManager(\EasySwoole\Rpc\NodeManager\TableManager::class);//设置节点管理器处理类,默认是EasySwoole\Rpc\NodeManager\FileManager
$rpc = new Rpc($config);
//获取所有服务节点列表
$nodeList = $config->getNodeManager()->allServiceNodes();
var_dump($nodeList);

go(function () use ($rpc) {
    $client = $rpc->client();
    //调用服务
    $serviceClient = $client->selectService('ser1');
    //创建执行任务
    $serviceClient->createTask()->setAction('call1')->setArg(['arg' => 1])
        ->setOnSuccess(function (Response $response) {
            echo ($response->getMessage()).PHP_EOL;
        })->setOnFail(function () {
            echo ("请求失败1!\n");
        });

    //创建执行任务
    $serviceClient->createTask()->setAction('call3')
        ->setOnSuccess(function (Response $response) {
            echo ($response->getMessage()).PHP_EOL;
        })->setOnFail(function () {
            echo ("请求失败2!\n");
        });

    //创建执行任务
    $serviceClient2 = $client->selectService('ser2');
    $serviceClient2->createTask()->setAction('call1')
        ->setOnSuccess(function (Response $response) {
            echo ($response->getMessage()).PHP_EOL;
        })->setOnFail(function () {
            echo ("请求失败3!\n");
        });
    $client->exec();//开始执行
});