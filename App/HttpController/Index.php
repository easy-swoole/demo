<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 11:10
 */

namespace App\HttpController;


use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Console\TcpService;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;

class Index extends Controller
{
    function index()
    {

        $ip = ServerManager::getInstance()->getSwooleServer()->connection_info($this->request()->getSwooleRequest()->fd);
//        var_dump($ip);
        $this->response()->write('your ip:'.$ip['remote_ip']);
        $this->response()->write('Index Controller is run');
        // TODO: Implement index() method.
    }

    function test(){
        $this->response()->write("router test");
    }

    /**
     * request 使用方法
     */
    function requestMethod()
    {
        $request = $this->request();

        $data = $request->getRequestParam();//用于获取用户通过POST或者GET提交的参数（注意：若POST与GET存在同键名参数，则以POST为准）。 示例：
        $param1 = $request->getRequestParam('param1');
        $get = $request->getQueryParams();
        $post = $request->getParsedBody();

        $post_data = $request->getBody();


        $swoole_request = $request->getSwooleRequest();//获取当前的swoole_http_request对象。

        $cookie = $request->getCookieParams();
        $cookie1 = $request->getCookieParams('cookie1');

        $files = $request->getUploadedFiles();
        $file = $request->getUploadedFile('form1');


        $content = $request->getBody()->__toString();
        $raw_array = json_decode($content, true);


        $header = $request->getHeaders();

        $server = $request->getServerParams();

    }


    function onException(\Throwable $throwable): void
    {
        Logger::getInstance()->log($throwable->getMessage());
    }


    /**
     * response使用方法
     */
    function responseMethod(){
        $response = $this->response();
        $swoole_response = $response->getSwooleResponse();
        $response->withStatus(Status::CODE_OK);
        $response->write('response write.');
        $response->setCookie('cookie name','cookie value',time()+120);
        $response->redirect('/test');
        $response->withHeader('Content-type','application/json;charset=utf-8');

        if ($response->isEndResponse()==$response::STATUS_NOT_END){
            $response->end();
        }
    }


    protected function onRequest(?string $action): ?bool
    {
        if(0/*auth_fail*/){
            $this->response()->write('auth fail');
            return false;
        }else{
            return true or null;
        }
    }

}