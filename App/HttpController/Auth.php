<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 16:59
 */

namespace App\HttpController;

use App\Store\AuthToken;
use App\Utility\WeChatAccount;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Message\Status;
use EasySwoole\WeChat\Bean\OfficialAccount\JsAuthRequest;
use EasySwoole\WeChat\WeChat;
use Endroid\QrCode\QrCode;

/**
 * 授权登录验证
 * Class Auth
 * @package App\HttpController
 */
class Auth extends Base
{

    /**
     * 第一步: 申请登陆令牌并获取登陆二维码
     * 请在60秒内完成全部登陆操作否则令牌过期
     * @return void
     */
    function getLoginQrCode()
    {
        $token = AuthToken::getInstance()->createToken(60);
        $authLink = $this->host() . "/Auth/scanRedirect?token={$token}";

        $qrCode = new QrCode($authLink);
        $qrCode->setSize(200);
        $content = base64_encode($qrCode->writeString());
        $this->writeJson(Status::CODE_OK, [
            'token' => $token,
            'expire' => time() + 60,
            'content' => "data:image/png;base64,{$content}"
        ]);
    }

    /**
     * 第二步: 轮询获取登陆状态
     * 0 等待扫描 1 等待确认 2 已确认 3 已过期或不存在
     * @return void
     */
    function checkLoginQrCode()
    {
        $token = $this->request()->getRequestParam('token');
        $state = $token = AuthToken::getInstance()->getTokenState($token);
        $responseState = $state !== false ? $state : AuthToken::STATE_TOKEN_EXPIRED;
        $this->writeJson(Status::CODE_OK, ['state' => $responseState, 'time' => time()]);
    }

    /**
     * 第三步: 跳转微信授权页面
     * @return void
     * @throws \Exception
     */
    function scanRedirect()
    {
        $token = $this->request()->getRequestParam('token');
        if ($token) {
            $tokenState = AuthToken::getInstance()->getTokenState($token);
            if ($tokenState === 0) {
                $request = new JsAuthRequest;
                $request->setType(JsAuthRequest::TYPE_USER_INFO);
                $request->setRedirectUri($this->host() . "/Auth/scanCallback?token={$token}");
                $wechat = new WeChat(WeChatAccount::getInstance()->wechatConfig());
                $url = $wechat->officialAccount()->jsApi()->auth()->generateURL($request);
                $this->response()->redirect($url);
                AuthToken::getInstance()->setTokenState($token, AuthToken::STATE_WAIT_CONFIRM);
            } else {
                $this->renderTemplate('ScanError.html', ['error' => '登陆令牌不存在，请重新扫码登录']);
            }
        } else {
            $this->renderTemplate('ScanError.html', ['error' => '参数不完整，请重新扫码登录']);
        }
    }

    /**
     * 第四步: 微信授权成功回跳
     * 显示一个确认登陆页面 等待用户确认登陆
     * @return void
     * @throws \EasySwoole\WeChat\Exception\OfficialAccountError
     * @throws \EasySwoole\WeChat\Exception\RequestError
     */
    function scanCallback()
    {
        $code = $this->request()->getRequestParam('code');
        $token = $this->request()->getRequestParam('token');
        if ($code && $token) {
            $wechat = new WeChat(WeChatAccount::getInstance()->wechatConfig());
            $user = $wechat->officialAccount()->jsApi()->auth()->codeToUser($code);
            $openid = $user->getOpenid();
            Cache::getInstance()->set("OPENID_{$openid}", $user);
            $this->renderTemplate('ScanConfirm.html', ['token' => $token, 'nickname' => $user->getNickname()]);
        } else {
            $this->response()->write('code and token is required');
        }
    }

    /**
     * 第五步: 用户点击确认登录按钮
     * 显示一个登陆成功页面
     * @return void
     * @throws \Exception
     */
    function scanConfirm()
    {
        $token = $this->request()->getRequestParam('token');
        if ($token) {
            $tokenState = AuthToken::getInstance()->getTokenState($token);
            if ($tokenState === 1) {
                AuthToken::getInstance()->setTokenState($token, AuthToken::STATE_CONFIRMED);
                $this->renderTemplate('ScanSuccess.html');
                return;
            }
        }
        $this->renderTemplate('ScanError.html', ['error' => '登陆凭证失效，请刷新页面重新扫码登录']);
    }

    /**
     * 用户匿名登陆 TODO 此处应有验证码验证
     * 创建匿名权限令牌 该令牌链接后除了心跳不接受任何上发消息
     * @return void
     */
    function anonymousLogin()
    {
        $token = AuthToken::getInstance()->createToken(60, true);
        $this->writeJson(Status::CODE_OK, [
            'token' => $token,
            'expire' => time() + 60
        ]);
    }
}