<?php


namespace App\Device;


use EasySwoole\Actor\AbstractActor;
use EasySwoole\Actor\ActorConfig;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;

class DeviceActor extends AbstractActor
{
    private $fd;
    private $deviceId;
    private $lastHeartBeat;
    public static function configure(ActorConfig $actorConfig)
    {
       $actorConfig->setActorName('Device');
    }

    protected function onStart()
    {
        $this->lastHeartBeat = time();
        /*
         * 该参数是创建的时候传递的
         */
        $this->fd = $this->getArg()['fd'];
        $this->deviceId = $this->getArg()['deviceId'];
        //记录到table manager中
        DeviceManager::addDevice(new DeviceBean([
            'deviceId'=>$this->deviceId,
            'actorId'=>$this->actorId(),
            'fd'=>$this->fd
        ]));
        //推送消息
        ServerManager::getInstance()->getSwooleServer()->push($this->fd,"connect to server success,your actorId is {$this->actorId()}");
        //创建一个定时器，如果一个设备20s没有收到消息，自动下线
        $this->tick(20*2000,function (){
            if(time() - $this->lastHeartBeat > 20){
                $this->exit(-1);
            }
        });
    }

    protected function onMessage($msg)
    {
        if($msg instanceof Command){
            $this->lastHeartBeat = time();
            switch ($msg->getCommand()){
                case $msg::RECONNECT:{
                    DeviceManager::updateDeviceInfo($this->deviceId,[
                        'fd'=>$msg->getArg()
                    ]);
                    $this->fd = $msg->getArg();
                    Logger::getInstance()->console("deviceId {$this->deviceId}  at actorId {$this->actorId()} reconnect success");
                    ServerManager::getInstance()->getSwooleServer()->push($this->fd,"deviceId {$this->deviceId}  at actorId {$this->actorId()} reconnect success");
                    break;
                }
                case $msg::WS_MSG:{
                    $recv = $msg->getArg();
                    Logger::getInstance()->console("deviceId {$this->deviceId}  at actorId {$this->actorId()} recv ws msg: {$recv}");
                    ServerManager::getInstance()->getSwooleServer()->push($this->fd,'actor recv msg for hash '.md5($recv));
                    break;
                }
                case $msg::REPLY_MSG:{
                    $recv = $msg->getArg();
                    Logger::getInstance()->console("deviceId {$this->deviceId}  at actorId {$this->actorId()} recv reply msg: {$recv}");
                    ServerManager::getInstance()->getSwooleServer()->push($this->fd,'actor recv reply msg '.$recv);
                    //此处return 一个数据，会返回给客户端
                    return "actorId {$this->actorId()} recv {$recv}";
                    break;
                }
            }
        }
    }

    protected function onExit($arg)
    {
        if($arg == -1){
            if(ServerManager::getInstance()->getSwooleServer()->exist($this->fd)){
                ServerManager::getInstance()->getSwooleServer()->push($this->fd,"heartbeat lost,actor exit");
                ServerManager::getInstance()->getSwooleServer()->close($this->fd);
            }
        }
        DeviceManager::deleteDevice($this->deviceId);
        Logger::getInstance()->console("deviceId {$this->deviceId} at actorId {$this->actorId()} exit");
    }

    protected function onException(\Throwable $throwable)
    {
        Trigger::getInstance()->throwable($throwable);
    }
}