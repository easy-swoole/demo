<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-12
 * Time: 上午9:27
 */

namespace App\Log;

use EasySwoole\Trace\AbstractInterface\LoggerWriterInterface;

class LogHandler implements LoggerWriterInterface
{

    function writeLog($obj, $logCategory, $timeStamp)
    {
        // TODO: Implement writeLog() method.
        echo date('Y-m-d H:i:s', $timeStamp)."\t".$obj.PHP_EOL;
    }
}