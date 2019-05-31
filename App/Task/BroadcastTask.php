<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 20:23
 */

namespace App\Task;

use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\Config;

/**
 * 发送广播消息
 * Class BroadcastTask
 * @package App\Task
 */
class BroadcastTask extends AbstractAsyncTask
{

    /**
     * 执行投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param $flags
     * @return bool
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();

        foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
            $connection = $server->connection_info($userFd);
            if ($connection['websocket_status'] == 3) {  // 用户正常在线时可以进行消息推送
                $server->push($userInfo['fd'], $taskData['payload']);
            }
        }

        // 添加到离线消息
        $payload = json_decode($taskData['payload'], true);
        if ($payload['action'] == 103) {

            $userinfo = OnlineUser::getInstance()->get($taskData['fromFd']);
            $payload['fromUserFd'] = 0;
            $payload['action'] = WebSocketAction::BROADCAST_LAST_MESSAGE;
            $payload['username'] = $userinfo['username'];
            $payload['avatar'] = $userinfo['avatar'];
            ChatMessage::getInstance()->saveMessage(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        }
        return true;
    }

    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}