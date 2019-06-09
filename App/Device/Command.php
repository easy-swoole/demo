<?php


namespace App\Device;


use EasySwoole\Spl\SplBean;

class Command extends SplBean
{
    const RECONNECT = 1;
    const WS_MSG = 2;
    const REPLY_MSG = 3;
    protected $command;
    protected $arg;

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command): void
    {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getArg()
    {
        return $this->arg;
    }

    /**
     * @param mixed $arg
     */
    public function setArg($arg): void
    {
        $this->arg = $arg;
    }
}