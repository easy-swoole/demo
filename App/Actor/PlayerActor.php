<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:00
 */

namespace App\Actor;


use EasySwoole\Actor\AbstractActor;
use EasySwoole\Actor\ActorConfig;
use EasySwoole\EasySwoole\Config;

class PlayerActor extends AbstractActor
{
    const STATUS_WAITING_READY = 1;
    const STATUS_INIT_POKER = 2;
    const STATUS_WAITING_COMPARE = 3;
    const STATUS_COMPARE = 4;
    private $fd;
    private $lastHeartBeat;

    static function configure(ActorConfig $actorConfig)
    {
        // TODO: Implement configure() method.
        $actorConfig->setActorName( 'player');

    }

    function onStart($arg)
    {
        // TODO: Implement onStart() method.
       $this->lastHeartBeat = time();
       $this->tick(5*1000,function (){
           //超过15s没有收到心跳，则认定客户端下线
          if(time() - $this->lastHeartBeat > 15){
              $this->exit();
          }
       });
    }

    function onMessage($msg)
    {
        // TODO: Implement onMessage() method.
    }

    function onExit($arg)
    {
        // TODO: Implement onExit() method.
        Config::getInstance()->delDynamicConf('fd_'.$this->fd);
    }

    protected function onException(\Throwable $throwable)
    {
        // TODO: Implement onException() method.
    }
}