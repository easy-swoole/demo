<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:48
 */

namespace App\Model\User;


use EasySwoole\Core\Component\Spl\SplBean;

class Bean extends SplBean
{
    protected $userId;
    protected $account;
    protected $password;
    protected $session;
    protected $addTime;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account): void
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session): void
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * @param mixed $addTime
     */
    public function setAddTime($addTime): void
    {
        $this->addTime = $addTime;
    }

    protected function initialize(): void
    {
        if(empty($this->addTime)){
            $this->addTime = time();
        }
        //默认md5是32 位，当从数据库中读出数据恢复为bean的时候，不对密码做md5
        if(strlen($this->password) == 32){
            $this->password = md5($this->password);
        }
    }
}