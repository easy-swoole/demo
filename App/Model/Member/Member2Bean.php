<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/28
 * Time: 11:47 AM
 */

namespace App\Model\Member;


use EasySwoole\Spl\SplBean;

class Member2Bean extends SplBean
{
    protected $member_id;
    protected $mobile;
    protected $name;
    protected $password;

    /**
     * @return mixed
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * @param mixed $member_id
     */
    public function setMemberId($member_id): void
    {
        $this->member_id = $member_id;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
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
}