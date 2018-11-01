<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 11:30
 */

namespace App\Process;


use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use Swoole\Process;

class ProcessTest extends AbstractProcess
{
    public function run(Process $process)
    {
        echo "process is run.\n";

        // TODO: Implement run() method.
    }

    public function onShutDown()
    {
        echo "process is onShutDown.\n";
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        echo "process is onReceive.\n";
        // TODO: Implement onReceive() method.
    }

}