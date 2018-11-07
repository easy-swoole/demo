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

class ServiceOne extends AbstractService
{
    function funcOne()
    {
        $arg = $this->getCaller()->getArgs();
        var_dump($arg);
        $this->getResponse()->setMessage('call at '.time());
    }

    function task(){
        /*
         * 如果是异步响应，请手动构建数据包
         */
        $fd = $this->getCaller()->getClient()->getFd();
        $arg = $this->getCaller()->getArgs();
        var_dump($arg);
        TaskManager::async(function ()use($fd){
            $res = new Response();
            $res->setMessage('this is task response');
            $res->setStatus(Response::STATUS_SERVICE_OK);
            ServerManager::getInstance()->getSwooleServer()->send($fd,Rpc::dataPack($res->__toString()));
        });
        $this->getResponse()->setMessage('call at '.time());
    }
}