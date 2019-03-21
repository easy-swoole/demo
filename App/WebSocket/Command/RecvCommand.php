<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-21
 * Time: 14:14
 */

namespace App\WebSocket\Command;

/**
 * 服务端收到消息
 * Class RecvCommand
 * @package App\WebSocket\Command
 */
class RecvCommand
{
    const RECV_TEXT_MESSAGE = 101;  // 收到文本消息
    const RECV_IMAGE_MESSAGE = 102; // 收到图片消息
}