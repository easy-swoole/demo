<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;



use App\Device\Command;
use App\Device\DeviceActor;
use App\Device\DeviceManager;
use EasySwoole\Actor\Actor;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //注册Actor
        Actor::getInstance()->register(DeviceActor::class);
        Actor::getInstance()->setListenPort(9600)
            ->setTrigger(Trigger::getInstance())
            ->setListenAddress('0.0.0.0')
            ->setTempDir(EASYSWOOLE_TEMP_DIR);
        Actor::getInstance()->attachServer(ServerManager::getInstance()->getSwooleServer());
        //创建Table用来记录 fd与actor的映射关系
        DeviceManager::tableInit();
        $register->add($register::onOpen,function (\swoole_websocket_server $svr, \swoole_http_request $req){
            if(!isset($req->get['deviceId'])){
                ServerManager::getInstance()->getSwooleServer()->push($req->fd,'deviceId@length=8 is require');
                ServerManager::getInstance()->getSwooleServer()->close($req->fd);
                return;
            }
            $deviceId = $req->get['deviceId'];
            $info = DeviceManager::deviceInfo($deviceId);
            if($info){
                //说明是断线重连
                $command = new Command();
                $command->setCommand($command::RECONNECT);
                $command->setArg($req->fd);
                DeviceActor::client()->send($info->getActorId(),$command);
            }else{
                //第一次链接服务端
                DeviceActor::client()->create([
                    'deviceId'=>$deviceId,
                    'fd'=>$req->fd
                ]);
            }
        });

        $register->add($register::onMessage,function (\swoole_websocket_server  $server, \swoole_websocket_frame $frame){
            $info = DeviceManager::deviceInfoByFd($frame->fd);
            if($info){
                $com = new Command();
                $com->setCommand($com::WS_MSG);
                $com->setArg($frame->data);
                DeviceActor::client()->send($info->getActorId(),$com);
            }else{
                $server->close($frame->fd);
            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }
}