# 多进程爬虫
EasySwoole利用redis队列+定时器+task进程实现的一个多进程爬虫。直接上代码
## 添加Redis配置信息
修改配置文件，添加Redis配置
```
"REDIS"=>array(
    "HOST"=>'',
    "PORT"=>6379,
    "AUTH"=>""
 )
```
## 封装Redis
```
namespace App\Utility\Db;


use Conf\Config;

class Redis
{
    private $con;
    protected static $instance;
    protected $tryConnectTimes = 0;
    protected $maxTryConnectTimes = 3;
    function __construct()
    {
        $this->connect();
    }
    function connect(){
        $this->tryConnectTimes++;
        $conf = Config::getInstance()->getConf("REDIS");
        $this->con = new \Redis();
        $this->con->connect($conf['HOST'], $conf['PORT'],2);
        $this->con->auth($conf['AUTH']);
        if(!$this->ping()){
            if($this->tryConnectTimes <= $this->maxTryConnectTimes){
                return $this->connect();
            }else{
                trigger_error("redis connect fail");
                return null;
            }
        }
        $this->con->setOption(\Redis::OPT_SERIALIZER,\Redis::SERIALIZER_PHP);
    }
    static function getInstance(){
        if(is_object(self::$instance)){
            return self::$instance;
        }else{
            self::$instance = new Redis();
            return self::$instance;
        }
    }
    function rPush($key,$val){
        try{
            return $this->con->rpush($key,$val);
//            return $ret;
        }catch(\Exception $e){
            $this->connect();
            if($this->tryConnectTimes <= $this->maxTryConnectTimes){
                return $this->rPush($key,$val);
            }else{
                return false;
            }

        }

    }
    function lPop($key){
        try{
            return $this->con->lPop($key);
        }catch(\Exception $e){
            $this->connect();
            if($this->tryConnectTimes <= $this->maxTryConnectTimes){
                return $this->lPop($key);
            }else{
                return false;
            }

        }
    }
    function lSize($key){
        try{
            $ret = $this->con->lSize($key);
            return $ret;
        }catch(\Exception $e){
            $this->connect();
            if($this->tryConnectTimes <= $this->maxTryConnectTimes){
                return $this->lSize($key);
            }else{
                return false;
            }

        }
    }
    function getRedisConnect(){
        return $this->con;
    }
    function ping(){
        try{
            $ret = $this->con->ping();
            if(!empty($ret)){
                $this->tryConnectTimes = 0;
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

}
```
## 定义SysConst
```
namespace App\Utility;


class SysConst extends \Core\Component\SysConst
{
    const TASK_RUNNING_NUM = 'TASK_RUNNING_NUM';
}
```
## 封装队列
```
namespace App\Model;


use App\Utility\Db\Redis;

class Queue
{
    const QUEUE_NAME = 'task_list';
    static function set(TaskBean $taskBean){
        return Redis::getInstance()->rPush(self::QUEUE_NAME,$taskBean);
    }
    static function pop(){
        return Redis::getInstance()->lPop(self::QUEUE_NAME);
    }
}
```
## 封装TaskBean
```
namespace App\Model;


use Core\Component\Spl\SplBean;

class TaskBean extends SplBean
{
    /*
     * 仅仅做示例，curl opt 选项请自己写
     */
    protected $url;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    protected function initialize()
    {
        // TODO: Implement initialize() method.
    }
}
```

## 封装异步执行模型
```
namespace App\Model;


use App\Utility\SysConst;
use Core\AbstractInterface\AbstractAsyncTask;
use Core\Component\Logger;
use Core\Component\ShareMemory;
use Core\Utility\Curl\Request;

class Runner extends AbstractAsyncTask
{

    function handler(\swoole_server $server, $taskId, $fromId)
    {
        // TODO: Implement handler() method.
        //记录处于运行状态的task数量
        $share = ShareMemory::getInstance();
        $share->startTransaction();
        $share->set(SysConst::TASK_RUNNING_NUM,$share->get(SysConst::TASK_RUNNING_NUM)+1);
        $share->commit();
        //while其实为危险操作，while会剥夺进程控制权
        while (true){
            $task = Queue::pop();
            if($task instanceof TaskBean){
                $req = new Request($task->getUrl());
                $ret = $req->exec()->getBody();
                Logger::getInstance("curl")->console("finish url:".$task->getUrl());
            }else{
                break;
            }
        }
//        Logger::getInstance()->console("async task exit");
        $share->startTransaction();
        $share->set(SysConst::TASK_RUNNING_NUM,$share->get(SysConst::TASK_RUNNING_NUM)-1);
        $share->commit();
    }

    function finishCallBack(\swoole_server $server, $task_id, $resultData)
    {
        // TODO: Implement finishCallBack() method.
    }
}
```

## 注册事件
在Conf/Event.php中
- 在启动前先清理共享内存信息。
```
 function frameInitialized()
 {
     // TODO: Implement frameInitialized() method.
     ShareMemory::getInstance()->clear();
 }
```
- 注册定时器，定时去获取任务
```
    function onWorkerStart(\swoole_server $server, $workerId)
    {
        // TODO: Implement onWorkerStart() method.
        //为第一个进程添加唤起任务执行的定时器；
        if($workerId == 0){
            Timer::loop(1000,function (){
                $share = ShareMemory::getInstance();
                //请勿使得所有worker全部处于繁忙状态   危险操作
                if($share->get(SysConst::TASK_RUNNING_NUM) < 2){
                    AsyncTaskManager::getInstance()->add(Runner::class);
                }
            });
        }
    }
```
## 任务投递控制器
任意建立一个控制器，添加以下两个方法。
```
    function addTask(){
        $url = $this->request()->getRequestParam("url");
        if(empty($url)){
            $url = 'http://wiki.swoole.com/';
        }
        $bean = new TaskBean();
        $bean->setUrl($url);
        //做异步投递
        AsyncTaskManager::getInstance()->add(function ()use($bean){
           Queue::set($bean);
        });
        $this->response()->writeJson(200,null,"任务投递成功");
    }
    function status(){
        $num = ShareMemory::getInstance()->get(SysConst::TASK_RUNNING_NUM);
        $this->response()->writeJson(200,array(
           "taskRuningNum"=>$num
        ));
    }
```

## 执行
启动EasySwoole，访问addTask方法往Redis队列中添加任务，并等待执行结果。

>本例子仅供参考，未做详尽错误处理。


