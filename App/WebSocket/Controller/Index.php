<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;
use Exception;

class Index extends Base
{
    /**
     * 当前用户信息
     * @throws Exception
     */
    function info()
    {
        $info = $this->currentUser();
        if ($info) {
            $message = new UserInfo;
            $message->setIntro('欢迎使用easySwoole');
            $message->setUserFd($info['fd']);
            $message->setAvatar($info['avatar']);
            $message->setUsername($info['username']);
            $this->response()->setMessage($message);
        }
    }

    /**
     * 在线用户列表
     * @throws Exception
     */
    function online()
    {
        $table = OnlineUser::getInstance()->table();
        $users = array();

        foreach ($table as $user) {
            $users['user' . $user['fd']] = $user;
        }

        if (!empty($users)) {
            $message = new UserOnline;
            $message->setList($users);
            $this->response()->setMessage($message);
        }
    }

    function heartbeat()
    {
        $this->response()->setMessage('PONG');
    }
}