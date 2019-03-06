<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/26
 * Time: 12:31 PM
 */

namespace App\Model;


use App\Utility\Pool\MysqlObject;

/**
 * model写法1
 * 通过传入mysql连接去进行处理
 * Class BaseModel
 * @package App\Model
 */
class BaseModel
{
    private $db;

    function __construct(MysqlObject $dbObject)
    {
        $this->db = $dbObject;
    }

    protected function getDb():MysqlObject
    {
        return $this->db;
    }

    function getDbConnection():MysqlObject
    {
        return $this->db;
    }

}