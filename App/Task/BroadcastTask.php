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
        // 添加到离线消息
        $payload = json_decode($taskData['payload'], true);
        if ($payload['action'] == 103) {
            $userinfo              = $redis->hGet(AppConst::REDIS_ONLINE_KEY, $taskData['fromFd']);
            $payload['fromUserFd'] = 0;
            $payload['action']     = WebSocketAction::BROADCAST_LAST_MESSAGE;
            $payload['username']   = $userinfo['username'];
            $redis->lPush(AppConst::REDIS_LAST_MESSAGE_KEY, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $max = Config::getInstance()->getConf('SYSTEM.LAST_MESSAGE_MAX');
            $redis->lTrim(AppConst::REDIS_LAST_MESSAGE_KEY, 0, $max - 1);
        }
        
        //更新用户聊天消息总数
        $fromFd = $taskData['fromFd'];
        $userInfo_u = $redis->hGet(AppConst::REDIS_ONLINE_KEY, $fromFd);
        if ($userInfo_u)
        {
            $userInfo_u['msgCnt'] = $userInfo_u['msgCnt'] + 1;
            $redis->hDel(AppConst::REDIS_ONLINE_KEY, $fromFd);
            $redis->hSet(AppConst::REDIS_ONLINE_KEY, $fromFd, $userInfo_u);
        }
        return true;
    }

    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}