<?php


namespace EasySwoole\EasySwoole;


use App\Utility\MyRpc;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        ###### 注册 rpc 服务 ######
        /** rpc 服务端配置 */
        $config = new \EasySwoole\Rpc\Config();
        # 【可选】 设置 rpc 服务端节点 Id
        $config->setNodeId('EasySwooleRpcNode');
        # 【可选】设置 rpc 服务端服务名称
        $config->setServerName('EasySwoole'); // 默认 EasySwoole
        # 设置 rpc 服务端启动时发生异常时的异常捕获回调
        $config->setOnException(function (\Throwable $throwable) {

        });

        $serverConfig = $config->getServer();
        # 设置 rpc 服务端节点服务监听的地址
        // 单机部署内部调用时可指定为 127.0.0.1
        // 分布式部署时多台调用时请填 0.0.0.0
        $serverConfig->setServerIp('127.0.0.1');

        // rpc 具体配置请看配置章节
        $rpc = new \EasySwoole\Rpc\Rpc($config);

        // 创建 Goods 服务
        $goodsService = new \App\RpcServices\Goods();
        // 添加 GoodsModule 模块到 Goods 服务中
        $goodsService->addModule(new \App\RpcServices\GoodsModule());
        // 添加 Goods 服务到服务管理器中
        $rpc->serviceManager()->addService($goodsService);

        // 创建 Common 服务
        $commonService = new \App\RpcServices\Common();
        // 添加 CommonModule 模块到 Common 服务中
        $commonService->addModule(new \App\RpcServices\CommonModule());
        // 添加 Common 服务到服务管理器中
        $rpc->serviceManager()->addService($commonService);

        // 此刻的rpc实例需要保存下来 或者采用单例模式继承整个Rpc类进行注册 或者使用Di
        Di::getInstance()->set('rpc_memory_object', $rpc);

        // 注册 rpc 服务
        $rpc->attachServer(ServerManager::getInstance()->getSwooleServer());


        ###### 使用继承 \EasySwoole\Rpc\Rpc 方式 注册 rpc 服务 ######
        /** rpc 服务端配置 */
        $config = new \EasySwoole\Rpc\Config();
        # 设置 rpc 服务端节点 Id
        $config->setNodeId('EasySwooleRpcNode2');
        # 设置 rpc 服务端服务名称
        $config->setServerName('EasySwoole2'); // 默认 EasySwoole
        # 设置 rpc 服务端启动时发生异常时的异常捕获回调
        $config->setOnException(function (\Throwable $throwable) {

        });

        $serverConfig = $config->getServer();
        # 设置 rpc 服务端节点服务监听的地址
        // 单机部署内部调用时可指定为 127.0.0.1
        // 分布式部署时多台调用时请填 0.0.0.0
        $serverConfig->setServerIp('127.0.0.1');
        $serverConfig->setListenAddress('0.0.0.0');
        $serverConfig->setListenPort(9601);

        // rpc 具体配置请看配置章节
        $rpc = MyRpc::getInstance($config);

        // 创建 Goods 服务
        $goodsService = new \App\RpcServices\Goods();
        // 添加 GoodsModule 模块到 Goods 服务中
        $goodsService->addModule(new \App\RpcServices\GoodsModule());
        // 添加 Goods 服务到服务管理器中
        $rpc->serviceManager()->addService($goodsService);

        // 创建 Common 服务
        $commonService = new \App\RpcServices\Common();
        // 添加 CommonModule 模块到 Common 服务中
        $commonService->addModule(new \App\RpcServices\CommonModule());
        // 添加 Common 服务到服务管理器中
        $rpc->serviceManager()->addService($commonService);

        // 此刻的rpc实例需要保存下来 或者采用单例模式继承整个Rpc类进行注册 或者使用Di

        // 注册 rpc 服务
        $rpc->attachServer(ServerManager::getInstance()->getSwooleServer());


        ###
        # 以上 2 种方式注册 rpc 服务，选其一即可
        ###
    }
}
