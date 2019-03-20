<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2019/3/20
 * Time: 16:18
 */

namespace App\Rpc;


class Siam
{

    private $args;

    public function __construct($args)
    {
        $this->args = $args;  // 所有参数
    }

    public function name()
    {
        return "My name is Siam -- Siam";
    }
}