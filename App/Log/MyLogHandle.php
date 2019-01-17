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
    public function log(string $str, $logCategory = null, int $timestamp = null): ?string
    {
        echo "这是自定义的log处理,模拟写入:[$logCategory]$str\n";

        return "这是自定义的log处理,模拟写入:[$logCategory]$str\n";//return到console组件
        // TODO: Implement log() method.
    }

    public function console(string $str, $category = null, $saveLog = true): ?string
    {
        echo "这是自定义的log处理,输出:$str\n";
        // TODO: Implement console() method.
        return "这是自定义的log处理,输出:$str\n";//return到console组件
    }


}