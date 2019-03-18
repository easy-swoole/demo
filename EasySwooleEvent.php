<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Utility\TrackerManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Message\Stream;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Trace\Bean\Tracker;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        //不建议在这拦截请求,可增加一个控制器基类进行拦截
        //如果真要拦截,判断之后return false即可
        $code = $request->getRequestParam('code');
        if (0/*empty($code)验证失败*/){
            $data = Array(
                "code" => Status::CODE_BAD_REQUEST,
                "result" => [],
                "msg" => '验证失败'
            );
            $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->withStatus(Status::CODE_BAD_REQUEST);
            return false;
        }

        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        $responseMsg = $response->getBody()->__toString();
        Logger::getInstance()->console("响应内容:".$responseMsg);
        //响应状态码:
//        var_dump($response->getStatusCode());
    }
}