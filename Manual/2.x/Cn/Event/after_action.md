## 请求方法结束后执行

假如你使用了单例模式，需要清理请求时的GET POST 等全局变量或本次请求的日志运行记录，就可以在此方法内执行。

```Php
protected function afterAction( $actionName ) : void
```

