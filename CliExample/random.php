<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午3:32
 */

// 加载库文件
require_once __DIR__."/../vendor/autoload.php";

// 随机生成4位字符串
$str = \EasySwoole\Utility\Random::character(4);
var_dump($str);

// 随机生成4位数字字符串
$number = \EasySwoole\Utility\Random::number(6);
var_dump($number);

// 随机从数组里面获取一个元素
$data = ['apple', 'orange', 'pear', 'grape'];
var_dump(\EasySwoole\Utility\Random::arrayRandOne($data));