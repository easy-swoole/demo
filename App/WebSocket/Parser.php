<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:39
 */

namespace App\WebSocket;


use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class Parser implements ParserInterface
{

    public function decode($raw, $client): ?Caller
    {
        // TODO: Implement decode() method.
    }

    public function encode(Response $response, $client): ?string
    {
        // TODO: Implement encode() method.
    }
}