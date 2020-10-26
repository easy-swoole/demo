<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Storage;

use EasySwoole\Spl\SplBean;

/**
 * 用户信息
 * Class UserBean
 * @package App\Storage
 */
class UserBean extends SplBean
{
    protected $email;

    protected $username;

    protected $userAvatar;

    protected $userPassword;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
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

    /**
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * @param mixed $userPassword
     */
    public function setUserPassword($userPassword): void
    {
        $this->userPassword = $userPassword;
    }
}
