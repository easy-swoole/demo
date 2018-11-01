<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/14
 * Time: 下午10:14
 */

namespace App\Rpc;


use EasySwoole\Component\Singleton;
use EasySwoole\Rpc\Rpc;

class RpcServer extends Rpc
{
    use Singleton;
}