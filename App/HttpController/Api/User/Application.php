<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-04-02
 * Time: 13:03
 */

namespace App\HttpController\Api\User;


use App\Model\Application\ApplicationModel;
use App\Model\Login\UserApplicationLoginModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;

class Application extends UserBase
{

    /**
     * getSecret
     * @Param(name="appId", alias="appId", required="", lengthMax="32")
     * @Param(name="userSession", alias="会员token", required="")
     * @throws \Throwable
     * @author Tioncico
     * Time: 15:06
     */
    public function getSecret()
    {
        $model = new ApplicationModel();
        $appInfo = $model->get($this->input('appId'));
        if (empty($appInfo)){
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '不存在该应用');
            return false;
        }

        $appLoginModel = new UserApplicationLoginModel();
        $appLoginInfo = $appLoginModel->get(['appId'=>$appInfo->appId,'userId'=>$this->who->userId]);
        if ($appLoginInfo){
            //更新过期时间
            $appLoginInfo->expireTime = time()+30*60;
            $result = $appLoginInfo->update();
            $appSecret = $appLoginInfo->appSecret;
        }else{
            $appLoginModel->userId = $this->who->userId;
            $appLoginModel->appId = $appInfo->appId;
            $appLoginModel->expireTime = time()+30*60;
            $appLoginModel->appSecret = md5($this->who->userId.$appInfo->appId.time());
            $result = $appLoginModel->save();
            $appSecret = $appLoginModel->appSecret;
        }
        if ($result===false){
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '更新数据失败');
            return false;
        }

        $this->writeJson(Status::CODE_OK, $appSecret, '获取appSecret成功');
    }
}