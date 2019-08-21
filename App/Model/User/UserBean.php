<?php

namespace App\Model\User;

/**
 * Class UserBean
 * Create With Automatic Generator
 * @property int userId |
 * @property string userName |
 * @property string userAccount |
 * @property string userPassword |
 * @property string phone |
 * @property string money |
 * @property int addTime |
 * @property string lastLoginIp |
 * @property int lastLoginTime |
 * @property string userSession |
 * @property int state |
 */
class UserBean extends \EasySwoole\Spl\SplBean
{
    protected $userId;

    protected $userName;

    protected $userAccount;

    protected $userPassword;

    protected $userAvatar;

    protected $phone;

    protected $money;

    protected $frozenMoney;

    protected $addTime;

    protected $lastLoginIp;

    protected $lastLoginTime;

    protected $userSession;

    protected $state;

    const STATE_PROHIBIT = 0;//禁用状态
    const STATE_NORMAL = 1;//正常状态

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }


    public function getUserId()
    {
        return $this->userId;
    }


    public function setUserName($userName)
    {
        $this->userName = $userName;
    }


    public function getUserName()
    {
        return $this->userName;
    }


    public function setUserAccount($userAccount)
    {
        $this->userAccount = $userAccount;
    }


    public function getUserAccount()
    {
        return $this->userAccount;
    }

    /**
     * @return mixed
     */
    public function getUserAvatar()
    {
        return $this->userAvatar;
    }

    /**
     * @param mixed $userAvatar
     */
    public function setUserAvatar($userAvatar): void
    {
        $this->userAvatar = $userAvatar;
    }


    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;
    }


    public function getUserPassword()
    {
        return $this->userPassword;
    }


    public function setPhone($phone)
    {
        $this->phone = $phone;
    }


    public function getPhone()
    {
        return $this->phone;
    }


    public function setMoney($money)
    {
        $this->money = $money;
    }


    public function getMoney()
    {
        return $this->money;
    }


    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;
    }


    public function getAddTime()
    {
        return $this->addTime;
    }


    public function setLastLoginIp($lastLoginIp)
    {
        $this->lastLoginIp = $lastLoginIp;
    }


    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }


    public function setLastLoginTime($lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;
    }


    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }


    public function setUserSession($userSession)
    {
        $this->userSession = $userSession;
    }


    public function getUserSession()
    {
        return $this->userSession;
    }


    public function setState($state)
    {
        $this->state = $state;
    }


    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getFrozenMoney()
    {
        return $this->frozenMoney;
    }

    /**
     * @param mixed $frozenMoney
     */
    public function setFrozenMoney($frozenMoney)
    {
        $this->frozenMoney = $frozenMoney;
    }
}