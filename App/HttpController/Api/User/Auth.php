<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-04-02
 * Time: 13:03
 */

namespace App\HttpController\Api\User;


use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Service\Common\VerifyService;
use App\Utility\Pool\MysqlPool;
use App\Utility\SwooleApi\User\Login;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use EasySwoole\Validate\Validate;

class Auth extends UserBase
{
    protected $whiteList = ['login', 'register'];

    /**
     * login
     * @Param(name="userAccount", alias="用户名", required="", lengthMax="32")
     * @Param(name="userPassword", alias="密码", required="", lengthMin="6",lengthMax="18")
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     * @author Tioncico
     * Time: 15:06
     */
    function login()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $model->userAccount = $param['userAccount'];
        $model->userPassword = md5($param['userPassword']);

        if ($userInfo = $model->login()) {
            $sessionHash = md5(time() . $userInfo->userId);
            $userInfo->update([
                'lastLoginIp'   => $this->clientRealIP(),
                'lastLoginTime' => time(),
                'userSession'   => $sessionHash
            ]);
            $rs = $userInfo->toArray();
            unset($rs['userPassword']);
            $rs['userSession'] = $sessionHash;
            $this->response()->setCookie('userSession', $sessionHash, time() + 3600, '/');
            $this->writeJson(Status::CODE_OK, $rs);
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '密码错误');
        }
    }


    function logout()
    {
        $sessionKey = $this->request()->getRequestParam('userSession');
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams('userSession');
        }
        if (empty($sessionKey)) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '尚未登入');
            return false;
        }
        $result = $this->getWho()->logout();
        if ($result) {
            $this->writeJson(Status::CODE_OK, '', "登出成功");
        } else {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', 'fail');
        }
    }


    function getInfo()
    {
        $this->writeJson(200, $this->getWho(), 'success');
    }
}