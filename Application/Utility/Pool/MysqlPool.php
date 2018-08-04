<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午3:08
 */

namespace App\Utility\Pool;

use EasySwoole\Component\Pool\AbstractPool;
use EasySwoole\Mysqli\Config;

class MysqlPool extends AbstractPool
{

    protected function createObject()
    {
        // TODO: Implement createObject() method.
        //这里的配置，请从Config读取
        $conf = new Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));

        $db = new MysqlPoolObj($conf);
        return $db;
    }
}