<?php
/**
 * Created by NetBeans IDE.
 * User: anit
 * Date: 2019-04-12
 */

namespace App\Beans;

use EasySwoole\Spl\SplBean;

/**
 *
 * 聊天室消息Bean
 * Class ChatMsgBean
 * @package App\Beans
 */
class ChatMsgBean extends SplBean
{
    protected $id;
    protected $chat_id;
    protected $type;
    protected $creator;
    protected $creator_id;
    protected $create_at;
    protected $duration;
    protected $msg;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getChat_id()
    {
        return $this->chat_id;
    }

    /**
     * @param mixed $chat_id
     */
    public function setChat_id($chat_id): void
    {
        $this->chat_id = $chat_id;
    }

    /**
     * @return mixed
     */
    public function getCreator_id()
    {
        return $this->creator_id;
    }

    /**
     * @param int $creator_id
     */
    public function setCreator_id($creator_id): void
    {
        $this->creator_id = $creator_id;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param int $creator
     */
    public function setCreator($creator): void
    {
        $this->creator = $creator;
    }
        
    /**
     * @return mixed
     */
    public function getCreate_at()
    {
        return $this->create_at;
    }

    /**
     * @param int $create_at
     */
    public function setCreate_at($create_at): void
    {
        $this->create_at = $create_at;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }
    
    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param int $msg
     */
    public function setMsg($msg): void
    {
        $this->msg = $msg;
    }
}