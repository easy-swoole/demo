<?php
/**
 * Created by PhpStorm.
 * User: anit
 * Date: 2019-03-26
 * Time: 14:56
 */

namespace App\WebSocket\Actions\Broadcast;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

/**
 * 广播排行榜消息
 * Class BroadcastMessage
 * @package App\WebSocket\Actions\Broadcast
 */
class BroadcastMessageRanking extends ActionPayload
{
    protected $action = WebSocketAction::BROADCAST_RANKING_BY_MESSAGE;
    protected $content;
    protected $type;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param mixed $content
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}