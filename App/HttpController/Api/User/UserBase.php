<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/26
 * Time: 5:39 PM
 */

namespace App\HttpController\Api\User;

use App\HttpController\Api\ApiBase;
use App\Model\User\UserBean;
use App\Model\User\UserModel;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Spl\SplBean;
use EasySwoole\Validate\Validate;

class UserBase extends ApiBase
{
    protected $who;
    //session的cookie头
    protected $sessionKey = 'userSession';
    //白名单
    protected $whiteList = ['login', 'register'];

    /**
     * onRequest
     * @param null|string $action
     * @return bool|null
     * @throws \Throwable
     * @author yangzhenyu
     * Time: 13:49
     */
    function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            //白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }
            //获取登入信息
            if (!$data = $this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
                return false;
            }
            //刷新cookie存活
            $this->response()->setCookie($this->sessionKey, $data->getUserSession(), time() + 3600, '/');

            return true;
        }
        return false;
    }

    /**
     * getWho
     * @return bool
     * @author yangzhenyu
     * Time: 13:51
     */
    function getWho(): ?UserBean
    {
        if ($this->who instanceof UserBean) {
            return $this->who;
        }
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }
        if (empty($sessionKey)) {
            return null;
        }
        $db = Mysql::defer('mysql');
        $userModel = new UserModel($db);
        $this->who = $userModel->getOneBySession($sessionKey);
        return $this->who;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        return null;
        // TODO: Implement getValidateRule() method.
    }
}