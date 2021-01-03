<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
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
