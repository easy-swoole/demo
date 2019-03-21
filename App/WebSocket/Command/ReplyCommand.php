<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-21
 * Time: 14:14
 */

namespace App\WebSocket\Command;

/**
 * 服务端回复消息
 * Class ReplyCommand
 * @package App\WebSocket\Command
 */
class ReplyCommand
{
    const REPLY_ROOM_INFO = 201;    // 刷新房间信息
    const REPLY_USER_INCOME = 202;  // 用户进入房间
    const REPLY_USER_OUTCOME = 203; // 用户退出房间
    const REPLY_BC_TEXT_MESSAGE = 204; // 用户退出房间
}