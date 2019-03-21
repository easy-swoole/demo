<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2019-03-21
 * Time: 14:25
 */

namespace App\Store;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

class OnlineUser
{
    use Singleton;  // 请在 EasySwooleEvent 全局期进行首次调用

    private $table;

    function __construct()
    {
        TableManager::getInstance()->add('onlineUser', [
            'fd' => ['type' => Table::TYPE_INT, 'size' => 4],
            'openid' => ['type' => Table::TYPE_STRING, 'size' => 64],
            'avatar' => ['type' => Table::TYPE_STRING, 'size' => 256],
            'nickname' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'onlineTime' => ['type' => Table::TYPE_INT, 'size' => 4],
            'anonymous' => ['type' => Table::TYPE_INT, 'size' => 1],
        ]);
        $this->table = TableManager::getInstance()->get('onlineUser');
    }

    /**
     * 创建在线用户
     * @param OnlineUserBean $userBean
     * @return mixed
     */
    function createUser(OnlineUserBean $userBean)
    {
        $data = $userBean->toArray(NULL, $userBean::FILTER_NOT_EMPTY);
        return $this->table->set($userBean->getFd(), $data);
    }

    /**
     * 删除在线用户
     * @param $fd
     */
    function deleteUser($fd)
    {
        $user = $this->table->get($fd);
        if ($user) {
            $this->table->del($fd);
        }
    }

    /**
     * 获取某个用户的信息
     * @param $fd
     * @return mixed
     */
    function getUser($fd)
    {
        return $this->table->get($fd);
    }

    /**
     * 获取所有用户
     * @return array
     */
    function getUsers()
    {
        $users = [];
        foreach ($this->table as $user) {
            $users[] = [
                'openid' => $user['openid'],
                'avatar' => $user['avatar'],
                'nickname' => $user['nickname'],
                'anonymous' => $user['anonymous'],
            ];
        }
        return $users;
    }
}