<?php
/**
 * User: XueSi
 * Date: 2021/7/27 16:16
 * Author: Longhui <1592328848@qq.com>
 */
declare(strict_types=1);

namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\Rpc\Config;

class MyRpc extends \EasySwoole\Rpc\Rpc
{
    use Singleton;

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }
}
