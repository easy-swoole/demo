<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:14
 */

namespace App\HttpController;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Swoole\ServerManager;

class Index extends Controller
{

    //测试路径 /index.html
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
    //测试路径 /test/index.html
    function test()
    {
        $ip = ServerManager::getInstance()->getServer()->connection_info($this->request()->getSwooleRequest()->fd);
        var_dump($ip);
        $ip2 = $this->request()->getHeaders();
        var_dump($ip2);
        $this->response()->write('index controller test');
    }

    /*
     * protected 方法对外不可见
     *  测试路径 /hide/index.html
     */
    protected function hide()
    {
        var_dump('this is hide method');
    }

    protected function actionNotFound($action): void
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
        $this->response()->write("{$action} is not exist");
    }

    function a()
    {
        $this->response()->write('index controller router');
    }

    function a2()
    {
        $this->response()->write('index controller router2');
    }

    function test2(){
        $this->response()->write('this is controller test2 and your id is '.$this->request()->getRequestParam('id'));
    }
}