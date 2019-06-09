<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/18 0018
 * Time: 9:40
 */

namespace App\HttpController;



use App\Device\Command;
use App\Device\DeviceActor;
use App\Device\DeviceManager;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        /*
         * http://easyswoole.com/wstool.html 测试设备客户端
         * 服务器测试地址  ws://127.0.0.1:9501/?deviceId=12345678
         */
        $deviceId = $this->request()->getRequestParam('deviceId');
        $msg = $this->request()->getRequestParam('msg');
        if(empty($deviceId)){
            $this->writeJson(400,null,'deviceId is require');
            return;
        }
        if(empty($msg)){
            $this->writeJson(400,null,'msg is require');
            return;
        }
        $info = DeviceManager::deviceInfo($deviceId);
        if($info){
            $com = new Command();
            $com->setCommand($com::REPLY_MSG);
            $com->setArg($msg);
            $reply = DeviceActor::client()->send($info->getActorId(),$com);
            $this->writeJson(200,$reply,'send to device success');
        }else{
            $this->writeJson(400,null,'deviceId is not exist');
        }
    }
}