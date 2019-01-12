<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-12
 * Time: 上午9:34
 */

namespace App\HttpController\Log;


use App\HttpController\Base;
use EasySwoole\EasySwoole\Logger;

class Index extends Base
{
    function index()
    {
        Logger::getInstance()->log('这是自定义写入的日志','notice');
        Logger::getInstance()->console('这是自定义输出的日志','类别',false);//默认输出之后还会写入,第三个参数false则不写入
    }
}