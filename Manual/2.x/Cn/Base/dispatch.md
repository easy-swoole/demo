# URL解析规则

仅支持`PATHINFO`模式的 URL 解析，且与控制器名称(方法)保持一致，控制器搜索规则为优先完整匹配模式

## 解析规则

在没有路由干预的情况下，内置的解析规则支持无限级嵌套目录，如下方两个例子所示

> **info no-icon**
>
> <http://serverName/api/auth/login>
>
> 对应执行的方法为 \App\Controller\Api\Auth::login()
>
> <http://serverName/a/b/c/d/f>
>
> 如果 f 为控制器名，则执行的方法为 \App\Controller\A\B\C\D\F::index()
>
> 如果 f 为方法名，则执行的方法为 \App\Controllers\A\B\C\D::f()

## 解析层级

理论上 easySwoole 支持无限层级的URL -> 控制器映射，但出于系统效率和防止恶意 URL 访问， 系统默认为3级，若由于业务需求，需要更多层级的URL映射匹配，请于框架初始化事件中向 DI 注入常量`SysConst::CONTROLLER_MAX_DEPTH` ，值为 URL 解析的最大层级，如下代码，允许 URL 最大解析至5层

```php
public function frameInitialize(): void
{
	Di::getInstance()->set(SysConst::CONTROLLER_MAX_DEPTH, 5);
}
```

