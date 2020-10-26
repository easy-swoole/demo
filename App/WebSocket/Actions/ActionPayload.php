<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
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
