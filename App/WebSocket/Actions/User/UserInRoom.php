<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\WebSocket\Actions\User;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserInRoom extends ActionPayload
{
    protected $action = WebSocketAction::USER_IN_ROOM;

    protected $info;

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = $info;
    }
}
