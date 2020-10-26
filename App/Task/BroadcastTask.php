<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Task;

use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use EasySwoole\EasySwoole\ServerManager;
use App\WebSocket\WebSocketAction;
use EasySwoole\Task\AbstractInterface\TaskInterface;
use Throwable;

/**
 * 发送广播消息
 * Class BroadcastTask
 * @package App\Task
 */
class BroadcastTask implements TaskInterface
{
    protected $taskData;

    public function __construct($taskData)
    {
        $this->taskData = $taskData;
    }

    /**
     * 执行投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param $flags
     * @return bool
     */
    public function run(int $taskId, int $workerIndex)
    {
        $taskData = $this->taskData;
        /** @var \Swoole\WebSocket\Server $server */
        $server = ServerManager::getInstance()->getSwooleServer();

        foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
            $connection = $server->connection_info((int)$userFd);
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

    public function onException(Throwable $throwable, int $taskId, int $workerIndex)
    {
        throw $throwable;
    }
}
