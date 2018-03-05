<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:47
 */

namespace App\Model\User;


use App\Model\Model;

class User extends Model
{
    protected $table = 'user_list';

    function register(Bean $bean)
    {
        return $this->dbConnector()->insert($this->table,$bean->toArray());
    }

    function delete(Bean $bean)
    {
        
    }
}