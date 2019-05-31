<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:54
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;
use Exception;

/**
 * 基础控制器
 * Class Base
 * @package App\WebSocket\Controller
 */
class Base extends Controller
{

    /**
     * 获取当前的用户
     * @return array|string
     * @throws Exception
     */
    protected function currentUser()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        return OnlineUser::getInstance()->get($client->getFd());
    }

}