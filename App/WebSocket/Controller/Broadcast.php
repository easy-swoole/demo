<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Task\BroadcastTask;
use App\Task\BroadcastMessageRankingTask;
use App\Utility\AppConst;
use App\WebSocket\WebSocketAction;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use App\WebSocket\Actions\Broadcast\BroadcastMessageRanking;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class Broadcast extends Controller
{
    /**
     * 发送消息给房间内的所有人
     * @throws \Exception
     */
    function roomBroadcast()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        $broadcastPayload = $this->caller()->getArgs();
        if (!empty($broadcastPayload) && isset($broadcastPayload['content'])) {
            
            //统计更新
            //更新用户聊天消息总数
            $redis = Redis::getInstance()->getConnect();
            if ($broadcastPayload['type'] == 103){
                
                $userInfo_u = $redis->hGet(AppConst::REDIS_ONLINE_KEY, $fromFd);
                if ($userInfo_u)
                {
                    $userInfo_u['msgCnt'] = $userInfo_u['msgCnt'] + 1;
                    $redis->hDel(AppConst::REDIS_ONLINE_KEY, $fromFd);
                    $redis->hSet(AppConst::REDIS_ONLINE_KEY, $fromFd, $userInfo_u);
                    
                    $username = $userInfo_u['username'];
                    
                    $redis->hDel(AppConst::REDIS_USERNAME_TO_USERINFO, $username);
                    $redis->hSet(AppConst::REDIS_USERNAME_TO_USERINFO, $username, $userInfo_u);
                    
                    $redis->zIncrBy(AppConst::REDIS_MESSAGE_RANK_KEY, 1, $username);
                }
            }    
            
            $message = new BroadcastMessage;
            $message->setFromUserFd($client->getFd());
            $message->setContent($broadcastPayload['content']);
            $message->setType($broadcastPayload['type']);
            $message->setSendTime(date('H:i'));
            
            TaskManager::async(new BroadcastTask(['payload' => $message->__toString(), 'fromFd' => $client->getFd()]));
            
            $message_rank = new BroadcastMessageRanking;
            $message_rank->setType(WebSocketAction::BROADCAST_RANKING_BY_MESSAGE);
            $message_rank->setContent('message_rank_test');
            
            TaskManager::async(new BroadcastMessageRankingTask(['payload' => $message_rank->__toString(), 'fromFd' => $client->getFd()]));
        }
        
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}