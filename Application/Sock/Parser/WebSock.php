<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/6
 * Time: 下午2:53
 */

namespace App\Sock\Parser;


use App\Sock\Controller\Web;
use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class WebSock implements ParserInterface
{

    public static function decode($raw, $client)
    {
        // TODO: Implement decode() method.
        $command = new CommandBean();
        $json = json_decode($raw,1);
        $command->setControllerClass(Web::class);
        $command->setAction($json['action']);
        $command->setArg('content',$json['content']);
        return $command;
    }

    public static function encode(string $raw, $client): ?string
    {
        // TODO: Implement encode() method.
        /*
         * 注意，return ''与return null不一样，空字符串一样会回复给客户端，比如在服务端主动心跳测试的场景
         */
        if(strlen($raw) == 0){
            return null;
        }
        return $raw;
    }
}