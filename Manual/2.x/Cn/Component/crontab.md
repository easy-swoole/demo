# Crontab 定时器
EasySwoole支持用户根据Crontab规则去添加定时器。时间最小粒度是1分钟。

## 实现原理
在主进程中，注册好各个任务规则和回调，服务启动后，在自定义进程内，通过定时器检测有没有待执行任务，若有则投递给异步进程异步执行。
解析规则通过https://github.com/dragonmantank/cron-expression实现。

## 示例代码
EasySwooleEvent.php中
use EasySwoole\Core\Component\Crontab\CronTab;
```
    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        CronTab::getInstance()->addRule('test1','@daily',function (){
            var_dump('run only once every daty');
        })->addRule('test2','*/1 * * * *',function (){
            var_dump('run per min at'.time());
        })->addRule('test3','*/2 * * * *',function (){
            var_dump('run per 2min at'.time());
        });
    }
```