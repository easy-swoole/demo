<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/8 0008
 * Time: 15:16
 */

namespace App\HttpController;


use App\Utility\Excel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Utility\Random;
use EasySwoole\VerifyCode\Conf;
use EasySwoole\VerifyCode\VerifyCode;

class Index extends Controller
{
    /**
     * 验证码验证方式(仅供参考)
     * index
     * @author Tioncico
     * Time: 16:06
     */
    function index()
    {
        $a=new a();
        $this->response()->write('hello world');
    }


}