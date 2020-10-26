<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

\Swoole\Coroutine::create(function () {
    $client = new \Swoole\Coroutine\Client(SWOOLE_TCP);
    $client->set([
        'open_length_check' => true,
        'package_max_length' => 81920,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_body_offset' => 4,
    ]);
    if (!$client->connect('127.0.0.1', 9510)) {
        echo 'tcp connect fail!';
    }

    $sendBody = json_encode([
        'controller' => 'Index',
        'action' => 'index'
    ]);

    $client->send(pack('N', strlen($sendBody)) . $sendBody);


    $recvBody = $client->recv();
    $len = unpack('N', $recvBody)[1];
    var_dump(substr($recvBody, 4, (int)$len));
});
