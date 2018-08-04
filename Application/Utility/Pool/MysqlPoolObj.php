<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午3:08
 */

namespace App\Utility\Pool;


use EasySwoole\Component\Pool\PoolObjectInterface;
use EasySwoole\Mysqli\Mysqli;

class MysqlPoolObj extends Mysqli implements PoolObjectInterface
{

    /*
    * 此处业务中，出现异常，然后导致状态，业务没有提交。
    */
    function gc()
    {
        // TODO: Implement gc() method.
        $this->rollback();
        $this->getMysqlClient()->close();
    }
    /*
     * 此处业务中，出现异常，然后导致状态，业务没有提交。
     */
    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
        $this->rollback();
        $this->resetDbStatus();
    }
}