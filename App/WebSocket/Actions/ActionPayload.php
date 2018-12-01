<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:46
 */

namespace App\WebSocket\Actions;

use EasySwoole\Spl\SplBean;

/**
 * 前端动作封包
 * Class ActionPayload
 * @package App\WebSocket\Actions
 */
class ActionPayload extends SplBean
{
    protected $action;
}