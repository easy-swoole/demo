<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

\Swoole\Coroutine::create(function () {
    $client = new \Swoole\Coroutine\Http\Client('127.0.0.1', 9512);
    $ret = $client->upgrade('/');

    if ($ret) {
        $sendBody = json_encode([
            'controller' => 'Index',
            'action' => 'index'
        ]);
        $client->push($sendBody);

        var_dump($client->recv());
    }
});
