<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/18 0018
 * Time: 14:56
 */

namespace App\Utility;


use EasySwoole\Trace\AbstractInterface\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * 打印到控制台并记录日志
     * console
     * @param string $str
     * @param null   $category
     * @param bool   $saveLog
     * @return string|null
     * @author Tioncico
     * Time: 14:57
     */
    public function console(string $str, $category = null, $saveLog = true): ?string
    {
        // TODO: Implement console() method.
        echo $str;
    }

    /**
     * 自定义进行日志存储,比如存到数据库,存到文件,或者请求其他地方存储
     * log
     * @param string   $str
     * @param null     $logCategory
     * @param int|null $timestamp
     * @return string|null
     * @author Tioncico
     * Time: 14:56
     */
    public function log(string $str, $logCategory = null, int $timestamp = null): ?string
    {
        // TODO: Implement log() method.
        file_put_contents(getcwd()."/test.log",$str.PHP_EOL,FILE_APPEND);
    }


}