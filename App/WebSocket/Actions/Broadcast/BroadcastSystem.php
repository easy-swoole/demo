<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\Broadcast;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class BroadcastSystem extends ActionPayload
{
    protected $action = WebSocketAction::BROADCAST_SYSTEM;
}