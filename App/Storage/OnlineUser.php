<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

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
    public function __construct()
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
    public function set($fd, $username, $avatar)
    {
        return $this->table->set((string)$fd, [
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
    public function get($fd)
    {
        $info = $this->table->get((string)$fd);
        return is_array($info) ? $info : null;
    }

    /**
     * 更新一条用户信息
     * @param $fd
     * @param $data
     */
    public function update($fd, $data)
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
    public function delete($fd)
    {
        $info = $this->get((string)$fd);
        if ($info) {
            $this->table->del((string)$info['fd']);
        }
    }

    /**
     * 心跳检查
     * @param int $ttl
     */
    public function heartbeatCheck($ttl = 60)
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
    public function updateHeartbeat($fd)
    {
        $this->update($fd, [
            'last_heartbeat' => time()
        ]);
    }

    /**
     * 直接获取当前的表
     * @return Table|null
     */
    public function table()
    {
        return $this->table;
    }
}
