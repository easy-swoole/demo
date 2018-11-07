<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/7 0007
 * Time: 16:14
 */

namespace App\Utility\ConsoleCommand;


use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class Test implements \EasySwoole\EasySwoole\Console\CommandInterface
{
    public function exec(Caller $caller, Response $response)
    {
        //调用命令时,会执行该方法
        $args = $caller->getArgs();//获取命令后面的参数
        $response->setMessage("你调用的命令参数为:".json_encode($args));
        // TODO: Implement exec() method.
    }

    public function help(Caller $caller, Response $response)
    {
        //调用 help Test时,会调用该方法
        $help = <<<HELP

用法 : Test [arg...]

参数 : 
  arg 
 
HELP;

        return $help;

        // TODO: Implement help() method.
    }

}