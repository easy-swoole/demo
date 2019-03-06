<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/26
 * Time: 12:31 PM
 */

namespace App\Model;


use App\Utility\Pool\Mysql2Object;

/**
 * model写法1
 * 通过传入mysql连接去进行处理
 * Class BaseModel
 * @package App\Model
 */
class BaseModel2
{
    private $db;

    function __construct(Mysql2Object $dbObject)
    {
        $this->db = $dbObject;
    }

    protected function getDb():Mysql2Object
    {
        return $this->db;
    }

    function getDbConnection():Mysql2Object
    {
        return $this->db;
    }

}