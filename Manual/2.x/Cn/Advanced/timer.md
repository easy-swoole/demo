# 定时器
框架对原生的毫秒级定时器进行了封装，以便开发者快速调用 Swoole 的原生定时器，定时器类的命名空间为 `EasySwoole\Core\Swoole\Time\Timer`

> 注意： 定时器传入的时间参数单位为毫秒 按秒执行一定不要忘记 乘以 1000



## 循环执行

设置一个间隔时钟定时器，每隔一定的时间定时触发，直到进行 `clear` 操作才会停止，对应 Swoole 原生的定时器函数为 `swoole_timer_tick`

### 函数原型

```php
/**
* 循环调用
* @param int      $microSeconds 循环执行的间隔毫秒数 传入整数型
* @param \Closure $func 定时器需要执行的操作 传入一个闭包
* @param mixed    $args 操作参数 此处传入的参数会按顺序传给闭包
* @return int 返回整数型的定时器编号 可以用该编号停止定时器
*/
public static function loop($microSeconds, \Closure $func, $args = null)
```

### 示例代码

```php
// 每隔 10 秒执行一次
Timer::loop(10 * 1000, function () {
	echo "this timer runs at intervals of 10 seconds\n";
});
```



## 延时执行

设置一个延时定时器，延时指定的时间后触发对应的操作，只会执行一次操作，对应 Swoole 原生的定时器函数为 `swoole_timer_after`

### 函数原型

```php
/**
* 延时调用
* @param int      $microSeconds 需要延迟执行的时间
* @param \Closure $func 定时器需要执行的操作 传入一个闭包
* @param mixed    $args 操作参数 此处传入的参数会按顺序传给闭包
* @return int 返回整数型的定时器编号 可以用该编号停止定时器
*/
public static function delay($microSeconds, \Closure $func, $args = null)
```

### 示例代码

```php
// 10 秒后执行一次
Timer::delay(10 * 1000, function () {
	echo "ten seconds later\n";
});
```



## 清除定时器

> 注意: 该操作不能用于清除其他进程的定时器，只作用于当前进程

定时器创建成功时，会返回一个整数型编号，调用本函数传入该编号，即可提前停止定时器，对应 Swoole 原生的定时器函数为 `swoole_timer_clear`

### 函数原型

```php
/**
* 清除定时器
* @param int $timerId 定时器编号
* @author : evalor <master@evalor.cn>
*/
public static function clear($timerId)
```

### 示例代码

```php
// 创建一个延迟10秒后执行的定时器
$timerId = Timer::after(10 * 1000, function () {
	echo "timeout\n";
});

// 清除该定时器
var_dump(Timer::clear($timerId)); // bool(true)
var_dump($timer); // int(1)

// 定时器得不到执行 不输出：timeout
```



## 应用实例

> 注意：定时器不能在服务启动之前使用。在服务启动以后，添加的定时器仅仅在当前进程中有效。在workerStart事件中添加定时器时，请注意判断需要添加定时器的workerId,否在该定时器在每个进程中均会被执行

```php
// 为第一个 Worker 添加定时器
if ($workerId == 0) {
	Timer::loop(10 * 1000, function () {
		echo "timer in the worker number 0\n";
	});
}
```

```php
public static function mainServerCreate(ServerManager $server, EventRegister $register): void
{
  $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
      //如何避免定时器因为进程重启而丢失
      //例如在第一个进程 添加一个10秒的定时器
      if ($workerId == 0) {
      Timer::loop(10 * 1000, function () {
        time();
        // 从数据库，或者是redis中，去获取下个就近10秒内需要执行的任务
        // 例如:2秒后一个任务，3秒后一个任务 代码如下
        Timer::delay(2 * 1000, function () {
          //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
          Logger::getInstance()->console("time 2", false);
        });
        Timer::delay(3 * 1000, function () {
          //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
          Logger::getInstance()->console("time 3", false);
        });
      });
    }
  });
}
```

### 经典案例-订单状态超时监控
场景说明：在很多抢购的场景中，订单下单完成后，需要限制其付款时间，或者是在棋牌游戏中，需要对房间状态进行监控。那么我们
可以先把待监控的订单或者是房间压入redis队列中。那么利用定时器+异步进程，去实现对订单状态的循环监控。