<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-6
 * Time: 下午4:28
 */

namespace App\Crontab;


use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class TaskTwo extends AbstractCronTask
{

    public static function getRule(): string
    {
        // TODO: Implement getRule() method.
        return '*/2 * * * *';
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        return 'taskTwo';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId)
    {
        // TODO: Implement run() method.
        var_dump('run once every two minutes');
    }
}