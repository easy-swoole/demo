```
EasySwoole\Core\Component\Cache\Cache
```

```
function __construct()
{
    $num = intval(Config::getInstance()->getConf("EASY_CACHE.PROCESS_NUM"));
    if($num <= 0){
       return;
    }
    $this->cliTemp = new SplArray();
    //若是在主服务创建，而非单元测试调用
    if(ServerManager::getInstance()->getServer()){
        $this->processNum = $num;
        for ($i=0;$i < $num;$i++){
            $processName = "cache_process_{$i}";
            ProcessManager::getInstance()->addProcess($processName,CacheProcess::class,true);
        }
    }
}
```

```
/*
 * 默认等待0.01秒的调度
 */
public function get($key,$timeOut = 0.01)
```

```
public function set($key,$data)
{
```

```
function del($key)
```

```
function flush()
```

```
public function deQueue($key,$timeOut = 0.01)
```

```
public function enQueue($key,$data)
```

```
public function clearQueue($key)
```