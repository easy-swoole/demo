<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Response;

class Rpc extends Controller
{
    public function testA()
    {
        $result = [];
        $client = \EasySwoole\Rpc\Rpc::getInstance()->client();
        $client->addCall('test_a', 'getList', range(1, 1000000))
            ->setOnSuccess(function (Response $response) use (&$result) {
                $result[] = $response->getResult();
            })
            ->setOnFail(function (Response $response) {
                var_dump($response->getStatus());
            });
        // 设置超时时间
        $client->exec(100);

        // 获取当前调用统计次数 这里是服务端所提供的 因为本demo客户端及服务端部署在一起 所以方可调用
        $result['getList'] = \EasySwoole\Rpc\Rpc::getInstance()->statisticsTable('test_a')->get('getList');
        $this->writeJson(200, $result);
    }

    public function testB()
    {
        $result = [];
        $client = \EasySwoole\Rpc\Rpc::getInstance()->client();
        $client->addCall('test_b', 'getList')
            ->setOnSuccess(function (Response $response) use (&$result) {
                $result[] = $response->getResult();
            })
            ->setOnFail(function (Response $response) {
                var_dump($response->getStatus());
            });
        $client->exec();

        // 获取当前调用统计次数
        $result['getList'] = \EasySwoole\Rpc\Rpc::getInstance()->statisticsTable('test_b')->get('getList');
        $this->writeJson(200, $result);
    }

    public function testError()
    {
        $client = \EasySwoole\Rpc\Rpc::getInstance()->client();
        $client->addCall('test_a', 'testError')
            ->setOnSuccess(function (Response $response) use (&$result) {
                $result = $response->getResult();
            })
            ->setOnFail(function (Response $response) {
                var_dump($response->getStatus());
            });
        // 设置超时时间
        $client->exec(100);

        // 获取当前调用统计次数
        $this->writeJson(200, $result);
    }
}
