<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 16:49
 */

namespace App\WebSocket;

use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

/**
 * 消息解析器
 * Class WebSocketParser
 * @package App\WebSocket
 */
class WebSocketParser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {

    }

    public function encode(Response $response, $client): ?string
    {
        // TODO: Implement encode() method.
    }

}