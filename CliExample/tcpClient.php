<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/6 0006
 * Time: 16:22
 */
include "../vendor/autoload.php";
define('EASYSWOOLE_ROOT', realpath(dirname(getcwd())));
\EasySwoole\EasySwoole\Core::getInstance()->initialize();
/**
 * tcp 客户端1,不验证数据包,没有处理粘包
 */
go(function () {
    $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
    if (!$client->connect('127.0.0.1', 9502, 0.5)) {
        exit("connect failed. Error: {$client->errCode}\n");
    }
    $str = 'hello world';
    $client->send($str);
    var_dump(($client->recv()));
//    $client->close();
});