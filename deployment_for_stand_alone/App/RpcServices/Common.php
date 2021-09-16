<?php
/**
 * User: XueSi
 * Date: 2021/7/27 15:41
 * Author: Longhui <1592328848@qq.com>
 */
declare(strict_types=1);

namespace App\RpcServices;

use EasySwoole\Rpc\Service\AbstractService;

class Common extends AbstractService
{
    function serviceName(): string
    {
        return 'Common';
    }
}
