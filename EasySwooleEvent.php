<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Actor\PlayerActor;
use App\Actor\RoomActor;
use EasySwoole\Actor\Actor;
use EasySwoole\Component\Timer;
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
        Actor::getInstance()->register(RoomActor::class);
        Actor::getInstance()->register(PlayerActor::class);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        //协议解析全部走wensocket控制器
        $register->set($register::onOpen,function ($ser,\swoole_http_request $req){
            $actor = PlayerActor::invoke()->create($req->fd);
            Config::getInstance()->setDynamicConf('fd_'.$req->fd,$actor);
        });

        $register->set($register::onMessage,function (\swoole_websocket_server $server,\swoole_websocket_frame $frame){

        });

        //demo 项目，仅仅设置一个房间
        $register->set($register::onWorkerStart,function ($ser,$id){
            if($id == 0){
                Timer::getInstance()->after(1000,function (){
                    $actorId = RoomActor::invoke()->create();
                    Config::getInstance()->setDynamicConf('roomActor',$actorId);
                });
            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}