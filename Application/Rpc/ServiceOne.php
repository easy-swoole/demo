<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午12:32
 */

namespace App\Rpc;


use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Rpc\AbstractInterface\AbstractService;
use EasySwoole\Rpc\Bean\Response;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Trigger\Logger;

class ServiceOne extends AbstractService
{
    function funcOne()
    {
        $arg = $this->getCaller()->getArgs();
//        Logger::getInstance()->log('client arg is '.json_encode($arg));
        $this->getResponse()->setMessage('call at '.time());
    }

    function task(){
        /*
         * 如果是异步响应，请手动构建数据包
         */
        $fd = $this->getCaller()->getClient()->getFd();
        TaskManager::async(function ()use($fd){
            $res = new Response();
            $res->setMessage('this is task response');
            $res->setStatus($res::STATUS_SERVICE_OK);
            ServerManager::getInstance()->getSwooleServer()->send($fd,Rpc::dataPack($res->__toString()));
        });
    }
}