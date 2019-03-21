<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-21
 * Time: 14:28
 */

namespace App\Store;

use EasySwoole\Spl\SplBean;

class OnlineUserBean extends SplBean
{
    protected $fd;
    protected $openid;
    protected $avatar;
    protected $nickname;
    protected $onlineTime;
    protected $anonymous;

    /**
     * FdGetter
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * FdSetter
     * @param mixed $fd
     */
    public function setFd($fd): void
    {
        $this->fd = $fd;
    }

    /**
     * OpenidGetter
     * @return mixed
     */
    public function getOpenid()
    {
        return $this->openid;
    }

    /**
     * OpenidSetter
     * @param mixed $openid
     */
    public function setOpenid($openid): void
    {
        $this->openid = $openid;
    }

    /**
     * AvatarGetter
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * AvatarSetter
     * @param mixed $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * NicknameGetter
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * NicknameSetter
     * @param mixed $nickname
     */
    public function setNickname($nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * OnlineTimeGetter
     * @return mixed
     */
    public function getOnlineTime()
    {
        return $this->onlineTime;
    }

    /**
     * OnlineTimeSetter
     * @param mixed $onlineTime
     */
    public function setOnlineTime($onlineTime): void
    {
        $this->onlineTime = $onlineTime;
    }

    /**
     * AnonymousGetter
     * @return mixed
     */
    public function getAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * AnonymousSetter
     * @param mixed $anonymous
     */
    public function setAnonymous($anonymous): void
    {
        $this->anonymous = $anonymous;
    }
}