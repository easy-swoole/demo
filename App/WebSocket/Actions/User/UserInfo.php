<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\User;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

/**
 *
 * 用户获取自己的信息
 * Class UserInfo
 * @package App\WebSocket\Actions\User
 */
class UserInfo extends ActionPayload
{
    protected $action = WebSocketAction::USER_INFO;
    protected $username;
    protected $intro;
    protected $userFd;
    protected $avatar;

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
    public function getUserFd()
    {
        return $this->userFd;
    }

    /**
     * @param mixed $userFd
     */
    public function setUserFd($userFd): void
    {
        $this->userFd = $userFd;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * @param mixed $intro
     */
    public function setIntro($intro): void
    {
        $this->intro = $intro;
    }


}