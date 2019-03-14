<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/8 0008
 * Time: 15:16
 */

namespace App\HttpController;


use App\Utility\Excel;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Utility\Random;
use EasySwoole\VerifyCode\Conf;
use EasySwoole\VerifyCode\VerifyCode;

class Index extends Controller
{
    function index()
    {
        $this->response()->write('hello world');
    }
}

