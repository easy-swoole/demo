<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午2:51
 */

namespace App\HttpController\WebSock;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $content = file_get_contents(__DIR__.'/client.html');
        $this->response()->write($content);
        $this->response()->setCookie('token','123',time()+3600);
    }

    /*
     * 请调用who，获取fd
     * http://ip:9501/webSock/push/index.html?fd=xxxx
     */
    function push()
    {
        $fd = intval($this->request()->getRequestParam('fd'));
        $info = ServerManager::getInstance()->getServer()->connection_info($fd);
        if(is_array($info)){
            ServerManager::getInstance()->getServer()->push($fd,'push in http at '.time());
        }else{
            $this->response()->write("fd {$fd} not exist");
        }
    }


    function broadcast(){
        TaskManager::async(function (){
           //注意  connection_list是分页的
           //https://wiki.swoole.com/wiki/page/p-connection_list.html
           $list = ServerManager::getInstance()->getServer()->connection_list();
           foreach ($list as $fd){
               $info = ServerManager::getInstance()->getServer()->connection_info($fd);
               //注意
               if(is_array($info) && $info['websocket_status']){
                   ServerManager::getInstance()->getServer()->push($fd,'push in http at '.time());
               }
           }
        });
    }
}