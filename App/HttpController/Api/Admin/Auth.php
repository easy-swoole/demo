<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/26
 * Time: 5:39 PM
 */

namespace App\HttpController\Api\Admin;

use App\Model\Admin\AdminBean;
use App\Model\Admin\AdminModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use EasySwoole\Validate\Validate;

class Auth extends AdminBase
{
    protected $whiteList=['login'];


    function login()
    {
        $param = $this->request()->getRequestParam();
        $db = Mysql::defer('mysql');
        $model = new AdminModel($db);
        $bean = new AdminBean();
        $bean->setAdminAccount($param['account']);
        $bean->setAdminPassword(md5($param['password']));

        if ($rs = $model->login($bean)) {
            $bean->restore(['adminId' => $rs->getAdminId()]);
            $sessionHash = md5(time() . $rs->getAdminId());
            $model->update($bean, [
                'adminLastLoginTime' => time(),
                'adminLastLoginIp'   => $this->clientRealIP(),
                'adminSession'       => $sessionHash
            ]);
            $rs = $rs->toArray(null, SplBean::FILTER_NOT_NULL);
            unset($rs['adminPassword']);
            $rs['adminSession'] = $sessionHash;
            $this->response()->setCookie('adminSession', $sessionHash, time() + 3600, '/');
            $this->writeJson(Status::CODE_OK, $rs);
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '密码错误');
        }

    }

    function logout()
    {
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams('adminSession');
        }
        if (empty($sessionKey)) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '尚未登入');
            return false;
        }
        $db = Mysql::defer('mysql');
        $adminModel = new AdminModel($db);
        $result = $adminModel->logout($this->getWho());
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

    protected function getValidateRule(?string $action): ?Validate
    {
        $validate = null;
        switch ($action) {
            case 'login':
                $validate = new Validate();
                $validate->addColumn('account')->required()->lengthMax(32);
                $validate->addColumn('password')->required()->lengthMax(32);
                break;
            case 'logout':
                break;
        }
        return $validate;
    }
}