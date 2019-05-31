<?php

namespace App\HttpController;

/**
 * 登录系统
 * Class Register
 * @package App\HttpController
 */
class Login extends Base
{
    function index()
    {
        $this->render('login');
    }
}