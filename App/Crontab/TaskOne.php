<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;



class TaskOne extends AbstractCronTask
{
    public static function getRule(): string
    {
        return '*/2 * * * *';
    }
    
    public static function getTaskName(): string 
    {
        return 'taskOne';
    }
    
    public static function run(\Swoole_server $server, int $taskId, int $fromWorkerId, $flag = NULL)
    {
        var_dump('run once per hour');
    }
}