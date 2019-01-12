<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 19-1-12
 * Time: 上午9:43
 */
namespace App\Log;
use EasySwoole\Trace\AbstractInterface\LoggerInterface;

class MyLogHandle implements LoggerInterface{
    public function console(string $str, $category = null, $saveLog = true)
    {
        echo "这是自定义的log处理,输出:$str\n";
        // TODO: Implement console() method.
    }

    public function log(string $str, $logCategory, int $timestamp = null)
    {
        echo "这是自定义的log处理,模拟写入:[$logCategory]$str\n";
        // TODO: Implement log() method.
    }

}