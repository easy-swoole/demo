<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2019/3/20
 * Time: 15:58
 */

namespace App\Rpc;


class Index
{
    private $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function index()
    {
        return "My name is Siam -- Index";
    }
}