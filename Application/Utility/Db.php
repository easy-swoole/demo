<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:41
 */

namespace App\Utility;


use EasySwoole\Config;

class Db
{
    private $db;
    function __construct()
    {
        $conf = Config::getInstance()->getConf('MYSQL');
        $this->db = new \MysqliDb($conf['HOST'],$conf['USER'],$conf['PASSWORD'],$conf['DB_NAME']);
    }

    function dbConnector()
    {
        return $this->db;
    }
}