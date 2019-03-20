<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Utility\Pool\RedisPool;
use App\WebSocket\WebSocketEvents;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;

use EasySwoole\EasySwoole\Crontab\Crontab;
use App\Crontab\TaskOne;

use App\Utility\Pool\MysqlPool;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Europe/Guernsey');
        
        ###############注册Mysql
        $mysqlConf = PoolManager::getInstance()->register(MysqlPool::class, 
                Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));
        
        if ($mysqlConf === null)
        {
            throw new Exception("Mysql Pool注册失败!");
        }
        
        $mysqlConf->setMaxObjectNum(20)->setMinObjectNum(5);
        
        PoolManager::getInstance()->register(RedisPool::class, Config::getInstance()->getConf('REDIS.POOL_MAX_NUM'));
    }

    /**
     * mainServerCreate
     * @param EventRegister $register
     * @throws \Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {
        PoolManager::getInstance()->register(RedisPool::class, 20);
        
        ################### mysql/redis 热启动   #######################
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(MysqlPool::class)->preLoad(5);//最小创建数量
                
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(RedisPool::class)->preload(2);
                
            }
        });
        
        // 注册WS事件回调
        $conf = new \EasySwoole\Socket\Config();
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser);
        $dispatch = new Dispatcher($conf);

        // 收到客户端消息时的处理
        $register->set(EventRegister::onMessage, function (\swoole_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });

        // 链接打开和关闭时的处理
        $register->set(EventRegister::onOpen, [WebSocketEvents::class, 'onOpen']);
        $register->set(EventRegister::onClose, [WebSocketEvents::class, 'onClose']);

        // 启动时清理 在线用户列表直接清空
        $register->add($register::onWorkerStart, function (\swoole_server $server, $workerId) {
            if ($workerId == 0) {
                WebSocketEvents::cleanOnlineUser();
            }
        });
        
        //Crontab
        Crontab::getInstance()->addTask(TaskOne::class);
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data): void
    {

    }

}