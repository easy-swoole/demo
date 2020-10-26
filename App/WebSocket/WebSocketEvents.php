<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\WebSocket;

use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use App\Task\BroadcastTask;
use App\Utility\Gravatar;
use App\WebSocket\Actions\Broadcast\BroadcastAdmin;
use App\WebSocket\Actions\User\UserInRoom;

use App\WebSocket\Actions\User\UserOutRoom;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Utility\Random;

use \Exception;

/**
 * WebSocket Events
 * Class WebSocketEvents
 * @package App\WebSocket
 */
class WebSocketEvents
{
    /**
     * 打开了一个链接
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\Http\Request $request
     */
    public static function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request)
    {
        // 为用户分配身份并插入到用户表
        $fd = $request->fd;
        if (isset($request->get['username']) && !empty($request->get['username'])) {
            $username = $request->get['username'];
            $avatar = Gravatar::makeGravatar($username . '@swoole.com');
        } else {
            $random = Random::character(8);
            $avatar = Gravatar::makeGravatar($random . '@swoole.com');
            $username = '神秘乘客' . $random;
        }

        // 插入在线用户表
        OnlineUser::getInstance()->set($fd, $username, $avatar);

        // 发送广播告诉频道里的用户 有新用户上线
        $userInRoomMessage = new UserInRoom;
        $userInRoomMessage->setInfo(['fd' => $fd, 'avatar' => $avatar, 'username' => $username]);
        TaskManager::getInstance()->async(new BroadcastTask(['payload' => $userInRoomMessage->__toString(), 'fromFd' => $fd]));
        if (empty($request->get['is_reconnection']) || $request->get['is_reconnection'] == '0') {

            // 发送欢迎消息给用户
            $broadcastAdminMessage = new BroadcastAdmin;
            $broadcastAdminMessage->setContent("{$username}，欢迎乘坐EASYSWOOLE号特快列车，请系好安全带，文明乘车");
            $server->push($fd, $broadcastAdminMessage->__toString());

            // 提取最后10条消息发送给用户
            $lastMessages = ChatMessage::getInstance()->readMessage();
            $lastMessages = array_reverse($lastMessages);
            if (!empty($lastMessages)) {
                foreach ($lastMessages as $message) {
                    $server->push($fd, $message);
                }
            }
        }
    }

    /**
     * 链接被关闭时
     * @param \Swoole\Server $server
     * @param int $fd
     * @param int $reactorId
     * @throws Exception
     */
    public static function onClose(\Swoole\Server $server, int $fd, int $reactorId)
    {
        $info = $server->connection_info($fd);
        if (isset($info['websocket_status']) && $info['websocket_status'] !== 0) {

            // 移除用户并广播告知
            OnlineUser::getInstance()->delete($fd);
            $message = new UserOutRoom;
            $message->setUserFd($fd);
            TaskManager::getInstance()->async(new BroadcastTask(['payload' => $message->__toString(), 'fromFd' => $fd]));
        }
    }
}
