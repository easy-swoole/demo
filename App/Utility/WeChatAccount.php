<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 19:02
 */

namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\WeChat\Config;

class WeChatAccount
{
    use Singleton;

    private $wechatConfig;

    function __construct()
    {
        $officialAccount = \EasySwoole\EasySwoole\Config::getInstance()->getConf('WECHAT');

        $wechatConfig = new Config;
        $wechatConfig->setTempDir(EASYSWOOLE_ROOT . '/Temp');
        $wechatConfig->officialAccount()->setAppId($officialAccount['APP_ID']);
        $wechatConfig->officialAccount()->setAppSecret($officialAccount['APP_SECRET']);

        $this->wechatConfig = $wechatConfig;
    }

    /**
     * 获取微信配置
     * @return Config
     */
    function wechatConfig()
    {
        return $this->wechatConfig;
    }
}