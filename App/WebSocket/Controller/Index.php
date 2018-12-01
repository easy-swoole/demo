<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Utility\AppConst;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;

class Index extends Base
{
    /**
     * 当前用户信息
     * @throws \Exception
     */
    function info()
    {
        $info = $this->currentUser();
        if ($info) {
            $message = new UserInfo;
            $message->setIntro($info['intro']);
            $message->setUserFd($info['userFd']);
            $message->setAvatar($info['avatar']);
            $message->setUsername($info['username']);
            $this->response()->setMessage($message);
        }
    }

    /**
     * 在线用户列表
     * @throws \Exception
     */
    function online()
    {
        $list = $this->redis()->hGetAll(AppConst::REDIS_ONLINE_KEY);
        if ($list) {
            $message = new UserOnline;
            $message->setList($list);
            $this->response()->setMessage($message);
        }
    }
}