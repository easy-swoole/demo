<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-1
 * Time: 下午2:40
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    function index() {
        $this->actionNotFound('index');
    }
}