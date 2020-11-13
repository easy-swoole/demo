<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace EasySwoole\EasySwoole;

use App\Utility\HttpEvent;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        // 全局-http请求前
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, [HttpEvent::class, 'onRequest']);
        // 全局-http请求后
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_AFTER_REQUEST, [HttpEvent::class, 'afterRequest']);
        // trigger 重写
        Di::getInstance()->set(SysConst::TRIGGER_HANDLER, \App\Utility\Trigger::class);
        // logger重写
        Di::getInstance()->set(SysConst::LOGGER_HANDLER, \App\Utility\Logger::class);
    }

    public static function mainServerCreate(EventRegister $register)
    {

        // TODO: Implement mainServerCreate() method.
    }
}
