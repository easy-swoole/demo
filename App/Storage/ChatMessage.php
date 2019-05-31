<?php

namespace App\Storage;

use EasySwoole\Component\Singleton;

/**
 * 储存最近的10条消息
 * 使用文件锁避免多个协程间读取冲突
 * Class ChatMessage
 * @package App\Storage
 */
class ChatMessage
{
    use Singleton;

    protected $storage;
    protected $capacity;
    protected $initialization = false;

    /**
     * ChatMessage constructor.
     * @param int $capacity 消息的储存容量 超出容量会自动丢弃
     */
    function __construct(int $capacity = 10)
    {
        $this->capacity = $capacity;
        $this->storage = $this->createStorage();
        if (is_writeable($this->storage)) {
            $this->initialization = true;
        }
    }

    /**
     * 储存一条消息
     * @param $message
     * @return bool
     */
    public function saveMessage($message)
    {

        if ($handle = fopen($this->storage, 'r+')) {
            clearstatcache() && flock($handle, LOCK_EX);
            $content = fread($handle, filesize($this->storage));
            $cache = unserialize($content) ?? [];
            array_unshift($cache, $message);
            $cacheContent = serialize(array_slice($cache, 0, $this->capacity));
            return ftruncate($handle, 0) && rewind($handle) && fwrite($handle, $cacheContent) && fclose($handle);
        }

        return false;
    }

    /**
     * 读取全部消息
     * @return array|mixed
     */
    public function readMessage()
    {
        if ($handle = fopen($this->storage, 'r')) {
            clearstatcache() && flock($handle, LOCK_SH | LOCK_NB);
            $content = fread($handle, filesize($this->storage));
            $cache = unserialize($content) ?? [];
            return $cache;
        }

        return [];
    }

    /**
     * 创建一个文件来储存消息
     * 优先使用内存块设备存储
     * @return bool|string
     */
    private function createStorage()
    {
        $location = is_writeable('/dev/shm') ? '/dev/shm' : sys_get_temp_dir();
        $cache = $location . DIRECTORY_SEPARATOR . date('Ymd') . '.cached';
        if (file_put_contents($cache, 'a:0:{}', LOCK_EX)) {
            return $cache;
        }
        return false;
    }
}