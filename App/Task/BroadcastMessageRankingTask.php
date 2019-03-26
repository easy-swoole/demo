<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 20:23
 */

namespace App\Task;

use App\Utility\AppConst;
use App\Utility\Redis;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

/**
 * 发送消息排行榜广播消息
 * Class BroadcastMessageRankingTask
 * @package App\Task
 */
class BroadcastMessageRankingTask extends AbstractAsyncTask
{

    /**
     * 执行投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @return bool
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {        
        // TODO: Implement run() method.
        $redis = Redis::getInstance()->getConnect();
        $users = $redis->hGetAll(AppConst::REDIS_ONLINE_KEY);
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();
        foreach ($users as $userFd => $userInfo) {
            $connection = $server->connection_info($userFd);
            if ($connection['websocket_status'] == 3) { // 用户正常在线时可以进行消息推送
                $server->push($userFd, $taskData['payload']);
            }
        }
             
        return true;
    }

    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}