<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Task\BroadcastTask;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use EasySwoole\EasySwoole\Task\TaskManager;
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
            $message = new BroadcastMessage;
            $message->setFromUserFd($client->getFd());
            $message->setContent($broadcastPayload['content']);
            $message->setType($broadcastPayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            TaskManager::getInstance()->async(new BroadcastTask(['payload' => $message->__toString(), 'fromFd' => $client->getFd()]));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}