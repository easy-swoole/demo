<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\WebSocket;

class WebSocketAction
{
    // 1xx BROADCAST 广播类消息
    const BROADCAST_ADMIN = 101;   // 管理消息

    const BROADCAST_SYSTEM = 102;  // 系统消息

    const BROADCAST_MESSAGE = 103; // 用户消息

    const BROADCAST_LAST_MESSAGE = 104; // 最后消息

    // 2xx USER 用户类消息
    const USER_INFO = 201;         // 用户信息

    const USER_ONLINE = 202;       // 在线列表

    const USER_IN_ROOM = 203;      // 进入房间

    const USER_OUT_ROOM = 204;     // 离开房间
}
