<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:21
 */

namespace App\HttpController\Api;


use EasySwoole\Core\Http\Message\Status;

class User extends AbstractBase
{
    //onRequest返回false的时候，为拦截请求，不再往下执行方法
    protected $authTime;
    protected function onRequest($action): ?bool
    {
        $token = $this->request()->getRequestParam('token');
        if($token == '123'){
            $this->authTime = time();
            return true;
        }else{
            $this->writeJson(Status::CODE_UNAUTHORIZED,null,'权限验证失败');
            return false;
        }
    }

    //测试url路径/api/user/info/index.html?token=123
    function info()
    {
        $this->response()->write('auth time is  '.$this->authTime);
    }
}