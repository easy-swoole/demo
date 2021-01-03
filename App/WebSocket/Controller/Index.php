<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
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
    public function info()
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
    public function online()
    {
        $table = OnlineUser::getInstance()->table();
        $users = [];

        foreach ($table as $user) {
            $users['user' . $user['fd']] = $user;
        }

        if (!empty($users)) {
            $message = new UserOnline;
            $message->setList($users);
            $this->response()->setMessage($message);
        }
    }

    public function heartbeat()
    {
        $this->response()->setMessage('PONG');
    }
}
