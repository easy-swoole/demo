<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use App\Process\Inotify;
use App\Process\Test;
use App\Sock\Parser\WebSock;
use \EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\EventHelper;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;

Class EasySwooleEvent implements EventInterface {

    public function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        //注册worker start 事件
        $register->add($register::onWorkerStart,function (\swoole_server $server,$workerId){
            //为workerId为0的进程添加定时器
            if($workerId == 0){
               Core\Swoole\Time\Timer::loop(2000,function (){
                   Logger::getInstance()->console('timer run');
                   //给自定义进程发送数据
                   ProcessManager::getInstance()->writeByProcessName('test',time());
               });
            }
        });

        ProcessManager::getInstance()->addProcess('test',Test::class);

        ProcessManager::getInstance()->addProcess('autoReload',Inotify::class);

        EventHelper::registerDefaultOnMessage($register,new WebSock());

        //注册ws  握手回调，可以实现在握手的时候，鉴定用户身份
        $register->add($register::onHandShake,function (\swoole_http_request $request, \swoole_http_response $response){

            if(isset($request->cookie['token'])){
                $token = $request->cookie['token'];
                if($token == '123'){
                    //若存在token   且token  验证通过，则进入ws rfc规范中约定的验证
                    if (!isset($request->header['sec-websocket-key']))
                    {
                        //不接受握手
                        var_dump('shake fai1 3');
                        $response->end();
                        return false;
                    }
                    if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
                        || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
                    )
                    {
                        //不接受握手
                        var_dump('shake fai1 4');
                        $response->end();
                        return false;
                    }

                    $key = base64_encode(sha1($request->header['sec-websocket-key']
                        . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                        true));
                    $headers = array(
                        'Upgrade'               => 'websocket',
                        'Connection'            => 'Upgrade',
                        'Sec-WebSocket-Accept'  => $key,
                        'Sec-WebSocket-Version' => '13',
                        'KeepAlive'             => 'off',
                    );
                    foreach ($headers as $key => $val)
                    {
                        $response->header($key, $val);
                    }
                    //接受握手  发送验证后的header   还需要101状态码
                    $response->status(101);
                    var_dump('shake success at fd :'.$request->fd);
                    $response->end();
                }else{
//                 //不接受握手
                    var_dump('shake fail 2');
                    $response->end();
                }
            }else{
                //不接受握手
                 var_dump('shake fai1 1');
                $response->end();
            }
        });
    }

    public function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}