<?php
require '../../vendor/autoload.php';
define('EASYSWOOLE_ROOT','../../');
\EasySwoole\EasySwoole\Core::getInstance()->initialize();

go(function (){
    //模拟注册Actor ,若在整个easySwoole服务中，客户端不必重复注册，因为已经在全局事件中注册了
    \EasySwoole\Actor\Actor::getInstance()->setTempDir(EASYSWOOLE_ROOT.'Temp2')->register(\App\Actor\RoomActor::class)->setActorProcessNum(3)->setActorName('RoomActor');//一样需要注册
    //添加一个actor ，若成功返回actorId,若超出数目则-1
    $actorId = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->create([
        'arg'=>1,
        'time'=>time()
    ]);
    //单独退出某个actor
    $ret = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->exit($actorId,['test'=>'test']);
    //单独推送给某个actor
    $ret = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->push($actorId,'1234');
    //单独推送给全部actor
//    $ret = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->pushMulti([
//        "0001"=>'0001data',
//        '0022'=>'0022Data'
//    ]);
//    广播给全部actor
    $ret = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->broadcastPush('121212');
//    退出全部actor
    $ret = \EasySwoole\Actor\Actor::getInstance()->client(\App\Actor\RoomActor::class)->exitAll(['arg1'=>'1']);//全部退出,参数arg1=>1
    var_dump($ret);
});