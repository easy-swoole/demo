<?php
/**
 * User: XueSi
 * Date: 2021/7/27 16:39
 * Author: Longhui <1592328848@qq.com>
 */
declare(strict_types=1);

namespace App\HttpController;

use App\Utility\MyRpc;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Protocol\Response;

class RpcClientTwo extends Controller
{
    protected function getRpcObjWithExtend()
    {
        /** rpc 服务端配置 */
        $config = new \EasySwoole\Rpc\Config();
        # 设置 rpc 服务端节点 Id
        $config->setNodeId('EasySwooleRpcNode');
        # 设置 rpc 服务端服务名称
        $config->setServerName('EasySwoole'); // 默认 EasySwoole
        # 设置 rpc 服务端启动时发生异常时的异常捕获回调
        $config->setOnException(function (\Throwable $throwable) {

        });

        $serverConfig = $config->getServer();
        # 设置 rpc 服务端节点服务监听的地址
        // 单机部署内部调用时可指定为 127.0.0.1
        // 分布式部署时多台调用时请填 0.0.0.0
        $serverConfig->setServerIp('127.0.0.1');

        return MyRpc::getInstance($config);
    }

    // 测试访问地址：http://192.168.214.138:9501/RpcClientTwo/callRpcServerWithExtend
    public function callRpcServerWithExtend()
    {
        // 如果在同server中 直接用保存的rpc实例调用即可
        $rpc = $this->getRpcObjWithExtend();

        $ret = [];
        $client = $rpc->client();
        // client 全局参数
        $client->setClientArg([1,2,3]);
        /**
         * 调用商品列表
         */
        $ctx1 = $client->addRequest('Goods.GoodsModule.list');
        // 设置请求参数
        $ctx1->setArg(['a','b','c']);
        // 设置调用成功执行回调
        $ctx1->setOnSuccess(function (Response $response) use (&$ret) {
            $ret[] = [
                'list' => [
                    'msg' => $response->getMsg(),
                    'result' => $response->getResult()
                ]
            ];
        });

        /**
         * 调用信箱公共
         */
        $ctx2 = $client->addRequest('Common.CommonModule.mailBox');
        // 设置调用成功执行回调
        $ctx2->setOnSuccess(function (Response $response) use (&$ret) {
            $ret[] = [
                'mailBox' => [
                    'msg' => $response->getMsg(),
                    'result' => $response->getResult()
                ]
            ];
        });

        /**
         * 获取系统时间
         */
        $ctx2 = $client->addRequest('Common.CommonModule.serverTime');
        // 设置调用成功执行回调
        $ctx2->setOnSuccess(function (Response $response) use (&$ret) {
            $ret[] = [
                'serverTime' => [
                    'msg' => $response->getMsg(),
                    'result' => $response->getResult()
                ]
            ];
        });

        // 执行调用
        $client->exec();
        $this->writeJson(200, $ret);
    }
}
