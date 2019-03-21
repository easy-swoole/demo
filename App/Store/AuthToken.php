<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-20
 * Time: 17:17
 */

namespace App\Store;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use EasySwoole\Utility\Random;
use Swoole\Table;

class AuthToken
{
    use Singleton;  // 请在 EasySwooleEvent 全局期进行首次调用

    private $table;

    const STATE_WAIT_SCAN = 0;
    const STATE_WAIT_CONFIRM = 1;
    const STATE_CONFIRMED = 2;
    const STATE_TOKEN_EXPIRED = 3;
    const STATE_TOKEN_USED = 4;

    function __construct()
    {
        TableManager::getInstance()->add('authToken', [
            'state' => ['type' => Table::TYPE_INT, 'size' => 1],
            'token' => ['type' => Table::TYPE_STRING, 'size' => 16],
            'expire' => ['type' => Table::TYPE_INT, 'size' => 4],
            'openid' => ['type' => Table::TYPE_STRING, 'size' => 64],
            'anonymous' => ['type' => Table::TYPE_INT, 'size' => 1]
        ]);
        $this->table = TableManager::getInstance()->get('authToken');
    }

    /**
     * 创建一个登陆令牌
     * @param int $expire
     * @param bool $anonymous
     * @return bool|string
     */
    function createToken(int $expire = 60, $anonymous = false)
    {
        $anonymous = $anonymous ? 1 : 0;
        $token = $this->generateToken();
        $this->table->set($token, ['state' => AuthToken::STATE_WAIT_SCAN, 'token' => $token, 'expire' => time() + $expire, 'anonymous' => $anonymous, 'openid' => null]);
        return $token;
    }

    /**
     * 获取一个令牌的详细信息
     * @param $token
     * @return bool
     */
    function getToken($token)
    {
        return $this->table->get($token);
    }

    /**
     * 获取令牌当前的状态
     * @param $token
     * @return bool
     */
    function getTokenState($token)
    {
        $tableItem = $this->table->get($token);
        if ($tableItem) {
            if ($tableItem['expire'] < time()) {
                $this->table->del($tableItem['token']);
                return false;
            }
            return $tableItem['state'];
        }
        return false;
    }

    /**
     * 设置令牌的状态
     * @param $token
     * @param $state
     * @return bool
     */
    function setTokenState($token, $state)
    {
        $tableItem = $this->table->get($token);
        if ($tableItem) {
            $this->table->set($token, ['state' => $state]);
            return true;
        }
        return false;
    }

    /**
     * 设置某个令牌的OPENID
     * @param $token
     * @param $openid
     * @return bool
     */
    function setOpenId($token, $openid)
    {
        $tableItem = $this->table->get($token);
        if ($tableItem) {
            $this->table->set($token, ['openid' => $openid]);
            return true;
        }
        return false;
    }

    /**
     * 清理已经过期的token
     * @return void
     */
    function cleanupExpiredTokens()
    {
        foreach ($this->table as $tableItem) {
            if ($tableItem['expire'] < time()) {
                $this->table->del($tableItem['token']);
            }
        }
    }

    /**
     * 获取一个唯一的token
     * @return bool|string
     */
    private function generateToken()
    {
        $token = Random::character(16);
        while ($this->table->get($token)) {
            $token = Random::character(16);
        }
        return $token;
    }

    /**
     * 获取当前Table实例
     * @return Table|null
     */
    function table()
    {
        return $this->table;
    }
}