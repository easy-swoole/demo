<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\RpcService;

use EasySwoole\Rpc\AbstractService;

class TestB extends AbstractService
{
    protected function onRequest(?string $action): ?bool
    {
        // 前置操作 可进行ip拦截
        return true;
    }

    public function serviceName(): string
    {
        // 服务名称
        return 'test_b';
    }

    public function getList()
    {
        $this->response()->setResult([
            'testId' => '100001',
            'testName' => '服务B'
        ]);
        $this->response()->setMsg('get testA list success');
    }
}
