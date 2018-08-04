<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午10:35
 */

namespace App\Model;


use App\Utility\Pool\MysqlPoolObj;

class BaseOne
{
    private $db;
    function __construct(MysqlPoolObj $obj)
    {
        $this->db = $obj;
    }

    protected function getDb():MysqlPoolObj
    {
        return $this->db;
    }
}