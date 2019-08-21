<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/10/26
 * Time: 5:39 PM
 */

namespace App\HttpController\Api\Admin;

use App\HttpController\Api\ApiBase;
use App\Model\Admin\AdminBean;
use App\Model\Admin\AdminModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Validate\Validate;

class AdminBase extends ApiBase
{
    protected $who;
    //session的cookie头
    protected $sessionKey = 'adminSession';
    //白名单
    protected $whiteList = [];

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
            if (!$this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
                return false;
            }
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
    function getWho(): ?AdminBean
    {
        if ($this->who instanceof AdminBean) {
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
        $adminModel = new AdminModel($db);
        $this->who = $adminModel->getOneBySession($sessionKey);
        return $this->who;
    }

    protected function getValidateRule(?string $action): ?Validate
    {
        return null;
        // TODO: Implement getValidateRule() method.
    }
}