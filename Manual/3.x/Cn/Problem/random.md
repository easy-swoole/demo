# 随机生成问题

由于Swoole本身的原因，在使用随机数时，需要额外注意，如果在父进程内调用了`mt_rand`，不同的子进程内再调用`mt_rand`返回的结果会是相同的。所以必须在每个子进程内调用`mt_srand`重新播种。

> `shuffle`和`array_rand`等依赖随机数的`PHP`函数同样会受到影响

## 场景例子

在异步任务，异步进程中，都需要注意随机数播种的问题，如下面的例子

```php
mt_rand(0, 1);    // 此处调用了 mt_rand 已经在父进程内自动播种
$worker_num = 16;

// fork 进程
for ($i = 0; $i < $worker_num; $i++) {
    $process = new swoole_process('child_async', false, 2);
    $pid = $process->start();
}

function child_async(swoole_process $worker)
{
    mt_srand();  // 此处 必须要重新播种 否则会得到相同的结果
    echo mt_rand(0, 100) . PHP_EOL;
    $worker->exit();
}
```

