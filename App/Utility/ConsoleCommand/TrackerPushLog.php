<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/31
 * Time: 12:25 PM
 */

namespace App\Utility\ConsoleCommand;


use EasySwoole\EasySwoole\Console\CommandInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class TrackerPushLog implements CommandInterface
{

    public function exec(Caller $caller, Response $response)
    {
        // TODO: Implement exec() method.
    }

    public function help(Caller $caller, Response $response)
    {
        // TODO: Implement help() method.
    }
}