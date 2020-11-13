<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Utility;

use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class HttpEvent
{
    public static function onRequest(Request $request, Response $response)
    {
        //不建议在这拦截请求,可增加一个控制器基类进行拦截
        //如果真要拦截,判断之后return false即可
        $code = $request->getRequestParam('code');
        if (0/*empty($code)验证失败*/) {
            $data = [
                'code' => Status::CODE_BAD_REQUEST,
                'result' => [],
                'msg' => '验证失败'
            ];
            $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->withStatus(Status::CODE_BAD_REQUEST);
            return false;
        }

        return true;
    }

    public static function afterRequest(Request $request, Response $response)
    {
//        $responseMsg = $response->getBody()->__toString();
//        Logger::getInstance()->console('响应内容:'.$responseMsg);
        //响应状态码:
        var_dump($response->getStatusCode());
    }
}
