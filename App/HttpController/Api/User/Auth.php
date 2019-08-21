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
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use EasySwoole\Validate\Validate;

class Auth extends UserBase
{
    protected $whiteList = ['login', 'register'];

    function login()
    {
        $param = $this->request()->getRequestParam();
        $db = Mysql::defer('mysql');
        $model = new UserModel($db);
        $bean = new UserBean();
        $bean->setUserAccount($param['userAccount']);
        $bean->setUserPassword(md5($param['userPassword']));

        if ($rs = $model->login($bean)) {
            $bean->restore(['userId' => $rs->getUserId()]);
            $sessionHash = md5(time() . $rs->getUserId());
            $model->update($bean, [
                'lastLoginIp'   => $this->clientRealIP(),
                'lastLoginTime' => time(),
                'userSession'   => $sessionHash
            ]);
            $rs = $rs->toArray(null, SplBean::FILTER_NOT_NULL);
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
        $db = Mysql::defer('mysql');
        $userModel = new UserModel($db);
        $result = $userModel->logout($this->getWho());
        if ($result) {
            $this->writeJson(Status::CODE_OK, '', "登出成功");
        } else {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', 'fail');
        }
    }


    function getInfo()
    {
        $this->getWho()->setPhone(substr_replace($this->getWho()->getPhone(), '****', 3, 4));
        $this->writeJson(200, $this->getWho(), 'success');
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        $validate = null;
        switch ($action) {
            case 'login':
                $validate = new Validate();
                $validate->addColumn('userAccount')->required()->lengthMax(32);
                $validate->addColumn('userPassword')->required()->lengthMax(32);
                break;
            case 'getInfo':
                break;
            case 'logout':
                break;
        }
        return $validate;
    }
}