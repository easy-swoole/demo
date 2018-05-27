<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/15
 * Time: 下午3:01
 */

namespace App\Sock\Parser;


use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class Tcp implements ParserInterface
{

    /*
     * 假定，客户端与服务端都是明文传输。控制格式为 sericeName:actionName:args
     */
    public static function decode($raw, $client)
    {
        // TODO: Implement decode() method.
        $list = explode(":",trim($raw));
        $bean = new CommandBean();
        $controller = array_shift($list);
        if($controller == 'test'){
            $bean->setControllerClass(\App\Sock\Controller\Tcp::class);
        }
        $bean->setAction(array_shift($list));
        $arg = array_shift($list);
        $bean->setArg('test',$arg);
        return $bean;
    }

    public static function encode(string $raw, $client): ?string
    {
        // TODO: Implement encode() method.
        return $raw."\n";
    }
}