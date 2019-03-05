<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-05
 * Time: 20:08
 */

namespace App\Process;


use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;

class ProcessOne extends AbstractProcess
{

    public function run($arg)
    {
        // TODO: Implement run() method.
        Logger::getInstance()->console($this->getProcessName()." start");
        while (1){
            \co::sleep(5);
            Logger::getInstance()->console($this->getProcessName()." run");
        }
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }
}