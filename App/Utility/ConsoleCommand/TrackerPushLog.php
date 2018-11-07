<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/31
 * Time: 12:25 PM
 */

namespace App\Utility\ConsoleCommand;


use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Console\CommandInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class TrackerPushLog implements CommandInterface
{

    public function exec(Caller $caller, Response $response)
    {
        $args = $caller->getArgs();
        $command = array_shift($args);
        if ($command == 'enable') {
            Config::getInstance()->setDynamicConf('CONSOLE.TRACKER_PUSH_LOG', true);
            $str = 'enable tracker push log';
        } else if ($command == 'disable') {
            Config::getInstance()->setDynamicConf('CONSOLE.TRACKER_PUSH_LOG', false);
            $str = 'disable tracker push log';
        } else {
            $status = Config::getInstance()->getDynamicConf('CONSOLE.TRACKER_PUSH_LOG');
            $str = 'tracker push log is ' . ($status ? 'enable' : 'disable');
        }
        $response->setMessage($str);
    }

    public function help(Caller $caller, Response $response)
    {
        $help = <<<HELP

用法 : TrackerPushLog [enable|disable]

参数: 
  enable   开启跟踪日志推送
  disable  关闭跟踪日志推送
  
HELP;

        return $help;
    }
}