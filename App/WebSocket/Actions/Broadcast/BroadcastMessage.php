<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\WebSocket\Actions\Broadcast;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

/**
 * 广播客户消息
 * Class BroadcastMessage
 * @package App\WebSocket\Actions\Broadcast
 */
class BroadcastMessage extends ActionPayload
{
    protected $action = WebSocketAction::BROADCAST_MESSAGE;

    protected $fromUserFd;

    protected $content;

    protected $type;

    protected $sendTime;

    /**
     * @return mixed
     */
    public function getFromUserFd()
    {
        return $this->fromUserFd;
    }

    /**
     * @param mixed $fromUserFd
     */
    public function setFromUserFd($fromUserFd): void
    {
        $this->fromUserFd = $fromUserFd;
    }

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

    /**
     * @param mixed $content
     */
    public function setSendTime($sendTime): void
    {
        $this->sendTime = $sendTime;
    }
}
