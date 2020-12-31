<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        // 使用 DI 注册 onRequest 全局事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response): bool {
            // TODO: Implement onRequest() method.
            return true;
        });

        // 使用 DI 注册 afterRequest 全局事件
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_AFTER_REQUEST, function (Request $request, Response $response): void {
            // TODO: Implement afterRequest() method.
        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $config = new \EasySwoole\ORM\Db\Config(Config::getInstance()->getConf('MYSQL'));
        DbManager::getInstance()->addConnection(new Connection($config));
    }
}
