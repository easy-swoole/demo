<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/17 0017
 * Time: 9:10
 */
namespace App\TcpController;

use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Utility\CommandLine;

class Parser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        // TODO: Implement decode() method.
        $list = explode(":",trim($raw));
        $bean = new Caller();
        $controller = array_shift($list);
        $controller = "App\\TcpController\\{$controller}";
        $bean->setControllerClass($controller);
        $bean->setAction(array_shift($list));
        $bean->setArgs($list);
        return $bean;
    }

    public function encode(Response $response,$client): ?string
    {
        return $response;
    }
}