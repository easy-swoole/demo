<?php

namespace App\Actor;

use EasySwoole\Actor\ActorConfig;

class RoomActor extends \EasySwoole\Actor\AbstractActor
{
    /**
     * 当发送消息时的回调
     * onMessage
     * @param $msg
     * @author Apple
     * Time: 13:59
     */
    function onMessage($msg)
    {
        var_dump("actor".$this->actorId()."on message:" . $msg . PHP_EOL);
        return "on message success\n";
        // TODO: Implement onMessage() method.
    }

    /**
     * 当actor退出时执行的回调
     * onExit
     * @author Apple
     * Time: 13:57
     */
    public function onExit($arg)
    {
        var_dump($arg);
        var_dump("actor".$this->actorId() . "已经退出\n");
        return "on exit success\n";
        // TODO: Implement onExit() method.
    }

    /**
     * 当执行出现异常时的回调
     * onException
     * @param \Throwable $throwable
     * @author Apple
     * Time: 13:58
     */
    protected function onException(\Throwable $throwable)
    {
        // TODO: Implement onException() method.
    }

    /**
     * 当该Actor被创建的时候
     * onStart
     * @author Apple
     * Time: 13:58
     */
    function onStart($arg)
    {
        var_dump("actor".$this->actorId() . "on start");
        // TODO: Implement onStart() method.
        return "on start success\n";
    }

    static function configure(ActorConfig $actorConfig)
    {
        $actorConfig->setActorName('RoomActor');
        // TODO: Implement configure() method.
    }

}