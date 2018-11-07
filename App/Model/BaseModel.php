<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午1:49
 */

namespace App\Model;


use App\Utility\Pool\MysqlObject;

class BaseModel
{
    private $db;
    function __construct(MysqlObject $db)
    {
        $this->db = $db;
    }

    function getDbConnection():MysqlObject
    {
        return $this->db;
    }
}