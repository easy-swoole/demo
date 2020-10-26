<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace App\Parser;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class WebSocketParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        $data = json_decode($raw, true);
        $caller = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action = !empty($data['action']) ? $data['action'] : 'index';
        $param = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\WebsocketController\\{$controller}";
        $caller->setControllerClass($controller);
        $caller->setAction($action);
        $caller->setArgs($param);
        return $caller;
    }

    public function encode(Response $response, $client): ?string
    {
        return json_encode($response->getMessage());
    }
}