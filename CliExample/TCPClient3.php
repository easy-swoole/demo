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
 * tcp 客户端3,验证数据包处理粘包 以及转发到控制器写法
 */
go(function () {
    $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
    $client->set(
        [
            'open_length_check'     => true,
            'package_max_length'    => 81920,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,
            'package_body_offset'   => 4,
        ]
    );
    if (!$client->connect('127.0.0.1', 9504, 0.5)) {
        exit("connect failed. Error: {$client->errCode}\n");
    }

    $data = [
        'controller' => 'Index',
        'action'     => 'index',
        'param'      => [
            'name' => '仙士可'
        ],
    ];
    $str = json_encode($data);
    var_dump($str);
    $client->send(encode($str));
    $data = $client->recv();//服务器已经做了pack处理
    $data = decode($data);//需要自己剪切解析数据
    echo "服务端回复: $data \n";

    $data = [
        'controller' => 'Index',
        'action'     => 'args',
        'param'      => [
            'name' => '仙士可'
        ],
    ];
    $str = json_encode($data);
    $client->send(encode($str));
    $data = $client->recv();//服务器已经做了pack处理
    $data = decode($data);//需要自己剪切解析数据
    echo "服务端回复: $data \n";




//    $client->close();
});

/**
 * 数据包 pack处理
 * encode
 * @param $str
 * @return string
 * @author Tioncico
 * Time: 9:50
 */
function encode($str)
{
    return pack('N', strlen($str)) . $str;
}

function decode($str)
{
    $data = substr($str, '4');
    return $data;
}