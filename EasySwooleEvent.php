<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace EasySwoole\EasySwoole;

use App\RpcService\TestA;
use App\RpcService\TestB;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\NodeManager\TableManager;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\ServiceCall;
use Swoole\Table;
use Throwable;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 定义redis pool
        $redisPool = new RedisPool(new RedisConfig([
            'host' => '127.0.0.1'
        ]));
        // rpc 节点管理 采用 rpc组件的 redis manager
        $manager = new RedisManager($redisPool);

        // 也可采用 swoole table 进行节点管理
        /*$key = 'easyswoole-rpc';
        \EasySwoole\Component\TableManager::getInstance()->add($key, [
            'serviceName' => ['type' => Table::TYPE_STRING, 'size' => 32],
            'serviceVersion' => ['type' => Table::TYPE_STRING, 'size' => 8],
            'serverIp' => ['type' => Table::TYPE_STRING, 'size' => 15],
            'serverPort' => ['type' => Table::TYPE_INT, 'size' => 4],
            'nodeId' => ['type' => Table::TYPE_STRING, 'size' => 8],
            'lastHeartBeat' => ['type' => Table::TYPE_INT, 'size' => 4],
        ]);
        $table = \EasySwoole\Component\TableManager::getInstance()->get($key);
        $manager = new TableManager($table);*/

        // 配置rpc
        $config = new \EasySwoole\Rpc\Config();

        // 设置服务端 最大接收数据大小
        $config->setMaxPackage(1024 * 1024 * 10);

        // 设置client包大小
        $config->getClientConfig()->setClientSettings([
            'package_max_length' => 1024 * 1024 * 8
        ]);
        // 设置全局client调用成功及失败的回调
        $config->getClientConfig()->setOnGlobalSuccess(function (Response $response, ServiceCall $serviceCall) {
            var_dump($response->getMsg());
        });
        $config->getClientConfig()->setOnGlobalFail(function (Response $response, ServiceCall $serviceCall) {
            var_dump($response->getStatus());
        });

        // 设置rpc 对外暴露 监听的ip及端口
        $config->setListenAddress('0.0.0.0');
        $config->setListenPort(9600);
        $config->setNodeManager($manager);

        // 设置全局异常 避免服务内 未做好异常处理 导致rpc工作进程退出（工作进程退出会被manager进程重新拉起）
        $config->setOnException(function (Throwable $throwable) {
        });

        //设置本机服务节点ip，如果不指定，则默认用UDP广播得到的地址
        $config->setServerIp('127.0.0.1');

        // 可采用 单独暴露一个udp服务
        /*$config->getBroadcastConfig()->setListenAddress('0.0.0.0');
        $config->getBroadcastConfig()->setListenPort(9601);
        // 设置 广播地址
        $config->getBroadcastConfig()->setBroadcastAddress(['127.0.0.1:9601']);
        $config->getBroadcastConfig()->setEnableBroadcast(true);
        $config->getBroadcastConfig()->setEnableListen(true);
        $config->getBroadcastConfig()->setSecretKey('easyswoole');*/

        // rpc初始化
        Rpc::getInstance($config);


        // 如果本服务需要调用其它服务 需要进行添加 不需要跳过
        Rpc::getInstance()->add(new TestA());
        Rpc::getInstance()->add(new TestB());

        // 此时rpc 部署完成
        Rpc::getInstance()->attachToServer(ServerManager::getInstance()->getSwooleServer());
    }
}
