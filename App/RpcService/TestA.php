<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;
use Exception;
use Throwable;

class TestA extends AbstractService
{
    protected function onRequest(?string $action): ?bool
    {
        // 前置操作 可进行ip拦截
        return true;
    }

    public function serviceName(): string
    {
        // 服务名称
        return 'test_a';
    }

    public function getList()
    {
        $arr = range(0, 10);
        $this->response()->setResult([
            'testId' => '100001',
            'testName' => '服务A', $arr
        ]);
        $this->response()->setMsg('get testA list success');
    }

    public function testError()
    {
        throw new Exception('this is error');
    }

    public function onException(Throwable $throwable)
    {
        $this->response()->setResult($throwable->getMessage());
        $this->response()->setMsg($throwable->getMessage());
    }
}
