<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-03-18
 * Time: 22:22
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

abstract class AbstractBase extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $this->actionNotFound('index');
    }
}