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
        $param = $this->request()->getQueryParams();
        $hash = $this->request()->getCookieParams('verifyCodeHash');
        $time = $this->request()->getCookieParams('verifyCodeTime');
        if (md5(strtolower($param['verifyCode']). $time.'halt') == $hash) {
            //调用后过期
            $this->response()->setCookie('verifyCodeHash', null, -1);
            $this->response()->setCookie('verifyCodeTime', null, -1);
            $this->response()->write('success');
        }else{
            //调用后过期
            $this->response()->setCookie('verifyCodeHash', null, -1);
            $this->response()->setCookie('verifyCodeTime', null, -1);
            $this->response()->write('error');
        }
    }

    /**
     * 直接发送接口数据
     * verifyCode
     * @author Tioncico
     * Time: 16:00
     */
    function verifyCode(){
        $ttl = 120;
        $conf = new Conf();
        $VCode = new VerifyCode($conf);
        // 随机生成验证码
        $random = Random::character(4, '1234567890abcdefghijklmnopqrstuvwxyz');
        $code = $VCode->DrawCode($random);
        $result = [
            'verifyCode'     => $code->getImageBase64(),
            'verifyCodeTime' => time(),
        ];

        //自己后端保存该次验证码
        //也可查看本demo验证方法
        //保存验证码方法
        $hash = md5($code->getImageCode() . $result['verifyCodeTime'].'halt');
        $this->response()->setCookie("verifyCodeHash", $hash, $result['verifyCodeTime'] + $ttl);

        $this->response()->setCookie('verifyCodeTime', $result['verifyCodeTime'], $result['verifyCodeTime'] + $ttl);



        $this->writeJson(200, $result);
    }

    /**
     * 直接输出图片
     * verifyCode
     * @author Tioncico
     * Time: 16:00
     */
    function verifyCodeImg(){
        $ttl = 120;
        $conf = new Conf();
        $VCode = new VerifyCode($conf);
        // 随机生成验证码
        $random = Random::character(4, '1234567890abcdefghijklmnopqrstuvwxyz');
        $code = $VCode->DrawCode($random);
        $result = [
            'verifyCode'     => $code->getImageBase64(),
            'verifyCodeTime' => time(),
        ];

        //自己后端保存该次验证码
        //也可查看本demo验证方法
        //保存验证码方法
        $hash = md5($code->getImageCode() . $result['verifyCodeTime'].'halt');
        $this->response()->setCookie("verifyCodeHash", $hash, $result['verifyCodeTime'] + $ttl);

        $this->response()->setCookie('verifyCodeTime', $result['verifyCodeTime'], $result['verifyCodeTime'] + $ttl);

        $this->response()->withHeader('Content-type','image/jpg');
        $this->response()->write($code->getImageByte());
    }


}