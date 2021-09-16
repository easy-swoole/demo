<?php
/**
 * User: XueSi
 * Date: 2021/7/27 16:39
 * Author: Longhui <1592328848@qq.com>
 */
declare(strict_types=1);

namespace App\HttpController;

use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Protocol\Response;
use EasySwoole\Rpc\Rpc;

class RpcClientOne extends Controller
{
    protected function getRpcObjWithDi()
    {
        return Di::getInstance()->get('rpc_memory_object');
    }

    // 测试访问地址：http://192.168.214.138:9501/RpcClientOne/callRpcServerWithDi
    public function callRpcServerWithDi()
    {
        // 如果在同server中 直接用保存的rpc实例调用即可
        /** @var Rpc $rpc */
        $rpc = $this->getRpcObjWithDi();

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
