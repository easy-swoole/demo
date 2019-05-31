<?php

namespace App\HttpController;

use App\Storage\UserStorage;
use EasySwoole\FastCache\Cache;
use EasySwoole\Smtp\Mailer;
use EasySwoole\Smtp\Message\Html;
use EasySwoole\Utility\Random;
use EasySwoole\Validate\Validate;

use EasySwoole\Smtp\MailerConfig as MailerSet;

/**
 * 注册用户
 * Class Register
 * @package App\HttpController
 */
class Register extends Base
{
    /**
     * 渲染注册页面
     */
    function index()
    {
        $this->render('register', [
            'EnableMailCheck' => (bool)$this->cfgValue('CHECK_EMAIL'),
        ]);
    }

    /**
     *
     */
    function registerAccount()
    {
        $v = new Validate;
        $v->addColumn('email')->required('登录账号没有填/(ㄒoㄒ)/~~')->regex('^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$', '邮箱格式不对呀/(ㄒoㄒ)/~~');
        $v->addColumn('password')->alphaNum('密码只能是大小写字母和数字的组合/(ㄒoㄒ)/~~')->betweenLen(6, 18, '密码长度需要6-18位/(ㄒoㄒ)/~~');

        if ($this->validate($v)) {

            // 账号注册逻辑

        } else {
            $this->writeJson(200, ['code' => 1, $v->getError()->getErrorRuleMsg()]);
        }

    }

    // 发送邮件验证码
    function sendValidateCode()
    {
        $v = new Validate;
        $v->addColumn('email')->required('登录账号没有填/(ㄒoㄒ)/~~')->regex('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/', '邮箱格式不对呀/(ㄒoㄒ)/~~');

        if ($this->validate($v)) {
            $email = $v->getVerifiedData()['email'];

            // 是否开启邮件发送
            if (!$this->cfgValue('CHECK_EMAIL')) {
                $this->writeJson(200, ['code' => 1, 'msg' => '当前未开启邮箱验证，您可以无视验证码 O(∩_∩)O~~']);
                return false;
            }

            // 验证账号是否存在
            if (UserStorage::emailIsExist($email)) {
                $this->writeJson(200, ['code' => 1, 'msg' => '您已经有账号啦，赶紧去登录吧 O(∩_∩)O~~']);
                return false;
            }

            // 验证邮件发送间隔
            $cacheTime = Cache::getInstance()->get(md5($email));
            if ($cacheTime && $cacheTime['time'] + 60 > time()) {
                $this->writeJson(200, ['code' => 1, 'msg' => '一分钟内已经给您发过邮件啦，快去看看，或者稍后再发吧 O(∩_∩)O~~']);
                return false;
            }

            // 是否开启邮件发送
            if (!$this->cfgValue('EMAIL_SETTING')) {
                $this->writeJson(200, ['code' => 1, 'msg' => '当前未配置邮件设置，请联系管理员 O(∩_∩)O~~']);
                return false;
            } else {
                if (!$emailSet = $this->cfgValue('EMAIL_SETTING'))
                    if (empty($emailSet['SERVER'])) {
                        $this->writeJson(200, ['code' => 1, 'msg' => '邮件服务器未配置，请联系管理员 O(∩_∩)O~~']);
                        return false;
                    }
                if (empty($emailSet['USERNAME'])) {
                    $this->writeJson(200, ['code' => 1, 'msg' => '邮件账号未配置，请联系管理员 O(∩_∩)O~~']);
                    return false;
                }
            }

            // 发注册验证码邮件
            $validateCode = Random::number(6);

            $mailerSet = new MailerSet;
            $mailerSet->setPort($emailSet['PORT']);
            $mailerSet->setSsl($emailSet['SECURE']);
            $mailerSet->setServer($emailSet['SERVER']);
            $mailerSet->setUsername($emailSet['USERNAME']);
            $mailerSet->setPassword($emailSet['PASSWORD']);
            $mailerSet->setMailFrom($emailSet['FORM']);

            $mimeBean = new Html;
            $mimeBean->setSubject('微聊注册码');
            $mimeBean->setBody('您正在注册微聊，验证码为: ' . $validateCode);

            try {

                $mailer = new Mailer($mailerSet);
                $mailer->sendTo($email, $mimeBean);
                Cache::getInstance()->set(md5($email), ['time' => time(), 'code' => $validateCode]);
                $this->writeJson(200, ['code' => 1, 'msg' => '发送成功，请查收您的验证码 O(∩_∩)O~~']);
                return true;

            } catch (\Throwable $throwable) {
                $this->writeJson(200, ['code' => 0, 'msg' => '邮件发送失败/(ㄒoㄒ)/~~', 'data' => $throwable->getMessage()]);
                return false;
            }


        } else {
            $this->writeJson(200, ['code' => 1, 'msg' => $v->getError()->getErrorRuleMsg()]);
            return false;
        }

    }
}