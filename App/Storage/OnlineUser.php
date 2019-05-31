<?php

namespace App\Storage;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

/**
 * 在线用户
 * Class OnlineUser
 * @package App\Storage
 */
class OnlineUser
{
    use Singleton;
    protected $table;  // 储存用户信息的Table

    const INDEX_TYPE_ROOM_ID = 1;
    const INDEX_TYPE_ACTOR_ID = 2;

    /**
     * OnlineUser constructor.
     */
    function __construct()
    {
        TableManager::getInstance()->add('onlineUsers', [
            'fd' => ['type' => Table::TYPE_INT, 'size' => 8],
            'avatar' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'username' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'last_heartbeat' => ['type' => Table::TYPE_INT, 'size' => 4],
        ]);

        $this->table = TableManager::getInstance()->get('onlineUsers');
    }

    /**
     * 设置一条用户信息
     * @param $fd
     * @param $username
     * @param $avatar
     * @return mixed
     */
    function set($fd, $username, $avatar)
    {
        return $this->table->set($fd, [
            'fd' => $fd,
            'avatar' => $avatar,
            'username' => $username,
            'last_heartbeat' => time()
        ]);
    }

    /**
     * 获取一条用户信息
     * @param $fd
     * @return array|mixed|null
     */
    function get($fd)
    {
        $info = $this->table->get($fd);
        return is_array($info) ? $info : null;
    }

    /**
     * 更新一条用户信息
     * @param $fd
     * @param $data
     */
    function update($fd, $data)
    {
        $info = $this->get($fd);
        if ($info) {
            $fd = $info['fd'];
            $info = $data + $info;
            $this->table->set($fd, $info);
        }
    }

    /**
     * 删除一条用户信息
     * @param $fd
     */
    function delete($fd)
    {
        $info = $this->get($fd);
        if ($info) {
            $this->table->del($info['fd']);
        }
    }

    /**
     * 心跳检查
     * @param int $ttl
     */
    function heartbeatCheck($ttl = 60)
    {
        foreach ($this->table as $item) {
            $time = $item['time'];
            if (($time + $ttl) < $time) {
                $this->delete($item['fd']);
            }
        }
    }

    /**
     * 心跳更新
     * @param $fd
     */
    function updateHeartbeat($fd)
    {
        $this->update($fd, [
            'last_heartbeat' => time()
        ]);
    }

    /**
     * 直接获取当前的表
     * @return Table|null
     */
    function table()
    {
        return $this->table;
    }
}