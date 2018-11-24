<?php
namespace App\HttpController;
 use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\EasySwoole\ServerManager;
 class WebSocketTest extends Controller
{
     function index()
    {
        // TODO: Implement index() method.
        $content = file_get_contents(__DIR__.'/websocket.html');
        $this->response()->write($content);
    }
     /*
     * 请调用who，获取fd
     * http://ip:9501/push/index.html?fd=xxxx
     */
    function push()
    {
        $fd = intval($this->request()->getRequestParam('fd'));
        $server = ServerManager::getInstance()->getSwooleServer();
        $info =$server->getClientInfo($fd);
        if(is_array($info)){
            $server->push($fd,'push in http at '.time());
        }else{
            $this->response()->write("fd {$fd} not exist");
        }
    }
}
