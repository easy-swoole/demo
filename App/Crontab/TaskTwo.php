<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-6
 * Time: 下午4:28
 */

namespace App\Crontab;


use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\EasySwoole\Crontab\Crontab;

class TaskTwo extends AbstractCronTask
{

    public static function getRule(): string
    {
        // TODO: Implement getRule() method.
        // 定时周期 （每两分钟一次）
        return '*/2 * * * *';
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'taskTwo';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId)
    {
        // TODO: Implement run() method.
        // 定时任务处理逻辑

        // 可以获得当前任务的规则 任务下一次执行的时间 任务总计执行的次数
        $cron = Crontab::getInstance();
        $cron->resetTaskRule('taskTwo','*/5 * * * *'); // 可以重新设置某任务的执行规则
        $current = date('Y-m-d H:i:s');
        $rule = $cron->getTaskCurrentRule('taskTwo');
        $nextTime = date('Y-m-d H:i:s',$cron->getTaskNextRunTime('taskTwo'));
        $runCount = $cron->getTaskRunNumberOfTimes('taskTwo');

        var_dump("cron taskTwo run at {$current} currentRule: {$rule} next: {$nextTime} count: {$runCount}");
    }
}