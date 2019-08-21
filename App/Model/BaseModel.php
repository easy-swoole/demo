<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/8/20 0020
 * Time: 16:22
 */

namespace App\Model;

use EasySwoole\Mysqli\Mysqli;

class BaseModel
{
    protected $db;
    protected $table;
    function __construct(Mysqli $connection)
    {
        $this->db = $connection;
    }

    function getDbConnection():Mysqli
    {
        return $this->db;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }
}