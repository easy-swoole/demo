<?php

namespace App\Model\Admin;

/**
 * Class AdminBean
 * Create With Automatic Generator
 * @property int adminId |
 * @property string adminName |
 * @property string adminAccount |
 * @property string adminPassword |
 * @property string adminSession |
 * @property int adminLastLoginTime |
 * @property string adminLastLoginIp |
 */
class AdminBean extends \EasySwoole\Spl\SplBean
{
    protected $adminId;

    protected $adminName;

    protected $adminAccount;

    protected $adminPassword;

    protected $adminSession;

    protected $adminLastLoginTime;

    protected $adminLastLoginIp;


    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }


    public function getAdminId()
    {
        return $this->adminId;
    }


    public function setAdminName($adminName)
    {
        $this->adminName = $adminName;
    }


    public function getAdminName()
    {
        return $this->adminName;
    }


    public function setAdminAccount($adminAccount)
    {
        $this->adminAccount = $adminAccount;
    }


    public function getAdminAccount()
    {
        return $this->adminAccount;
    }


    public function setAdminPassword($adminPassword)
    {
        $this->adminPassword = $adminPassword;
    }


    public function getAdminPassword()
    {
        return $this->adminPassword;
    }


    public function setAdminSession($adminSession)
    {
        $this->adminSession = $adminSession;
    }


    public function getAdminSession()
    {
        return $this->adminSession;
    }


    public function setAdminLastLoginTime($adminLastLoginTime)
    {
        $this->adminLastLoginTime = $adminLastLoginTime;
    }


    public function getAdminLastLoginTime()
    {
        return $this->adminLastLoginTime;
    }


    public function setAdminLastLoginIp($adminLastLoginIp)
    {
        $this->adminLastLoginIp = $adminLastLoginIp;
    }


    public function getAdminLastLoginIp()
    {
        return $this->adminLastLoginIp;
    }
}