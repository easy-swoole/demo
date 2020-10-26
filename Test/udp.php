<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

\Swoole\Coroutine::create(function () {
    $client = new \Swoole\Coroutine\Client(SWOOLE_UDP);

    $sendBody = json_encode([
        'controller' => 'Index',
        'action' => 'index'
    ]);

    $client->sendto('127.0.0.1', 9511, $sendBody);


    $recvBody = $client->recv();
    var_dump(json_decode($recvBody));
});
