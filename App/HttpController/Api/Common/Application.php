<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-04-02
 * Time: 13:03
 */

namespace App\HttpController\Api\Common;


use App\Model\Login\UserApplicationLoginModel;
use App\Model\User\UserModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;

class Application extends CommonBase
{

    /**
     * getUserInfo
     * @Param(name="appId", alias="appId", required="", lengthMax="32")
     * @Param(name="appSecret", alias="appSecret", required="")
     * @throws \Throwable
     * @author Tioncico
     * Time: 15:06
     */
    public function getUserInfo()
    {
        $appLoginModel = new UserApplicationLoginModel();
        $appLoginInfo = $appLoginModel->get(['appId' => $this->input('appId'), 'appSecret' => $this->input('appSecret')]);
        //如果不存在,则说明授权不存在
        if (empty($appLoginInfo)) {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '授权记录不存在!');
            return false;
        }
        //判断过期时间
        if ($appLoginInfo->expireTime <= time()) {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '授权已过期!');
            return false;
        }

        //更新过期时间
        $appLoginInfo->expireTime = time() + 30 * 60;
        $result = $appLoginInfo->update();
        if($result===false){
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '更新过期信息失败!');
            return false;
        }

        //获取用户信息(除去token和密码的获取,demo没有其他字段,如果有,那就得加上头像等信息输出)
        $userModel = new UserModel();
        $userInfo = $userModel->field('userId,userAccount')->get($appLoginInfo->userId);

        $this->writeJson(Status::CODE_OK, $userInfo, '获取用户信息成功');
    }
}