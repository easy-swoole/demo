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
 * 聊天室Bean
 * Class ChatBean
 * @package App\Beans
 */
class ChatBean extends SplBean
{
    protected $id;
    protected $name;
    protected $type;
    protected $creator;
    protected $creator_id;
    protected $create_at;
    protected $duration;
    protected $subject;
    protected $password;
    protected $capacity;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator($creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @return mixed
     */
    public function getCreator_id()
    {
        return $this->creator_id;
    }

    /**
     * @param id $creator_id
     */
    public function setCreator_id($creator_id): void
    {
        $this->creator_id = $creator_id;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mix $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param int $capacity
     */
    public function setCapacity($capacity): void
    {
        $this->capacity = $capacity;
    }    
}