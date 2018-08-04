<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午1:20
 */

namespace App\HttpController;


use EasySwoole\Rpc\Bean\Response;
use EasySwoole\Trigger\Logger;

class Rpc extends Base
{
    function index()
    {
        $t = microtime(true);
        $client = \EasySwoole\Rpc\Rpc::getInstance()->client();
        $client->addCall('serviceOne','funcOne')
            ->success(function (Response $response){
                Logger::getInstance()->console($response->__toString());
            })
            ->fail(function (Response $response){
                Logger::getInstance()->console($response->__toString());
            });

        $client->addCall('serviceOne','task')
            ->success(function (Response $response){
                Logger::getInstance()->console($response->__toString());
            })
            ->fail(function (Response $response){
                Logger::getInstance()->console($response->__toString());
            });

        $client->exec(0.5);

        $t = round(microtime(true) - $t,3);
        $this->response()->write("rpc take {$t} s");
    }

    function allNodes()
    {
        var_dump(\EasySwoole\Rpc\Rpc::getInstance()->getAllServiceNodes());
    }
}