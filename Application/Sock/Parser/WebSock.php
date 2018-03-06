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

    public function decode($raw, $client)
    {
        // TODO: Implement decode() method.
        $command = new CommandBean();
        $json = json_decode($raw,1);
        $command->setControllerClass(Web::class);
        $command->setAction($json['action']);
        $command->setArg('content',$json['content']);
        return $command;
    }

    public function encode(string $raw, $client, $commandBean): ?string
    {
        // TODO: Implement encode() method.
        return $raw;
    }
}