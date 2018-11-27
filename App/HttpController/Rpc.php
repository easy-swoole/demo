<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午1:20
 */

namespace App\HttpController;

use App\Rpc\RpcServer;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Bean\Response;

class Rpc extends Controller
{
    /*
     * 具体使用看https://github.com/easy-swoole/rpc/
     */
    function index()
    {
        $rpc = RpcServer::getInstance();

        //虚拟一个服务节点//默认不注册本机的服务,单机测试自行虚拟
        $serviceNode = new \EasySwoole\Rpc\ServiceNode();
        $serviceNode->setServiceName('ServiceOne');
        $serviceNode->setServiceIp('127.0.0.1');
        $serviceNode->setServicePort(9601);
        $serviceNode->setNodeId('qwe');
        $rpc->nodeManager()->refreshServiceNode($serviceNode);

        $msg = null;
        $client = RpcServer::getInstance()->client();
        $client->selectService('ServiceOne')->callAction('a1')->setArg(
            [
                'callTime' => time()
            ]
        )->onSuccess(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
            $this->response()->write('success' . $response->getMessage());
        })->onFail(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
            $this->response()->write('fail' . $response->getStatus());
        })->setTimeout(1.5);

        /*$client->selectService('serviceOne')->callAction('a1')->onSuccess(function (\EasySwoole\Rpc\Task $task, \EasySwoole\Rpc\Response $response, ?\EasySwoole\Rpc\ServiceNode $serviceNode) {
            $this->response()->write('success' . $response->getMessage());
        });*/
        $client->call(1.5);
    }

    function allNodes()
    {
        $rpc = RpcServer::getInstance();

        //虚拟一个服务节点//默认不注册本机的服务,单机测试自行虚拟
        $serviceNode = new \EasySwoole\Rpc\ServiceNode();
        $serviceNode->setServiceName('serviceOne');
        $serviceNode->setServiceIp('127.0.0.1');
        $serviceNode->setServicePort(9601);
        $serviceNode->setNodeId('qwe');
        $rpc->nodeManager()->refreshServiceNode($serviceNode);
        var_dump(RpcServer::getInstance()->nodeManager()->allServiceNodes());
    }
}