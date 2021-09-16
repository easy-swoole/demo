<?php
/**
 * User: XueSi
 * Date: 2021/7/27 15:41
 * Author: Longhui <1592328848@qq.com>
 */
declare(strict_types=1);

namespace App\RpcServices;

use EasySwoole\Rpc\Service\AbstractServiceModule;

class CommonModule extends AbstractServiceModule
{
    function moduleName(): string
    {
        return 'CommonModule';
    }

    public function mailBox()
    {
        // 获取client 全局参数
        $this->request()->getClientArg();
        // 获取参数
        $this->request()->getArg();
        $this->response()->setResult([
            [
                'mailId' => '100001',
                'mailTitle' => '系统消息1',
            ],
            [
                'mailId' => '100001',
                'mailTitle' => '系统消息1',
            ],
        ]);
        $this->response()->setMsg('get mail list success');
    }

    public function serverTime()
    {
        $this->response()->setResult(time());
        $this->response()->setMsg('get server time success');
    }
}
