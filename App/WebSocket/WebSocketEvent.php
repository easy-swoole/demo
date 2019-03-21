<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 16:49
 */

namespace App\WebSocket;

use App\Store\AuthToken;
use App\Store\OnlineUser;
use App\Store\OnlineUserBean;
use EasySwoole\FastCache\Cache;
use EasySwoole\WeChat\Bean\OfficialAccount\User;

/**
 * WebSocket事件处理器
 * Class WebSocketEvent
 * @package App\WebSocket
 */
class WebSocketEvent
{
    protected $dispatcher;

    /**
     * 用户连接到WebSocket
     * @param \swoole_server $server
     * @param \swoole_http_request $req
     */
    static function onOpen(\swoole_server $server, \swoole_http_request $req)
    {
        $token = $req->get['token'];
        $tokenInfo = AuthToken::getInstance()->getToken($token);
        if ($tokenInfo && $tokenInfo['state'] === AuthToken::STATE_CONFIRMED) {  // 令牌必须是已经确认登陆的状态
            $userBean = new OnlineUserBean;
            $userBean->setOnlineTime(time());
            $anonymous = $tokenInfo['anonymous'];

            if ($anonymous) {  // 用户匿名登录
                $userBean->setAnonymous(1);
                $userBean->setFd($req->fd);
                $userBean->setOpenid('');
                $userBean->setAvatar('');
                $userBean->setNickname('游荡的灵魂');
            } else {  // 微信扫码登录
                $openid = $tokenInfo['openid'];
                /** @var User $userInfo */
                $userInfo = Cache::getInstance()->get("'OPENID_{$openid}'");
                $userBean->setAnonymous(0);
                $userBean->setFd($req->fd);
                $userBean->setOpenid($openid);
                $userBean->setAvatar($userInfo->getHeadimgurl());
                $userBean->setNickname($userInfo->getNickname());
            }

            // 创建在线用户
            OnlineUser::getInstance()->createUser($userBean);

        } else {
            $server->close($req->fd);
        }
    }

    /**
     * 用户断开链接
     * @param \swoole_server $server
     * @param int $fd
     * @param int $reactorId
     */
    static function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        $userInfo = OnlineUser::getInstance()->getUser($fd);
        if ($userInfo) {
            OnlineUser::getInstance()->deleteUser($fd);
        }
    }
}