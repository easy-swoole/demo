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

    /**
     * @return bool
     */
    function beforeUse(): bool
    {
        //使用前调用,当返回true，表示该对象可用。返回false，该对象失效，需要回收
        //根据个人逻辑修改,只要做好了断线处理逻辑,就可直接返回true
        return $this->getMysqlClient()->connected;
    }

}
