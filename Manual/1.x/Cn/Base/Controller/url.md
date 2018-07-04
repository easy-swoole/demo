# URL访问规则
仅支持 pathInfo 模式的 URL,且与控制器名称(方法)保持一致,控制器搜索规则为优先完整匹配模式。
## 路由规则
内置路由支持无限层级的路由,即Controller可以无限嵌套目录,如:

http://127.0.0.1:9501/api/auth/login

执行的方法为:\App\Controller\Api\Auth::login()

http://127.0.0.1:9501/a/b/c/d/f

如f为控制器名,执行的方法为:\App\Controller\A\B\C\D\F::index()
如F为方法名,执行的方法为:\App\Controllers\A\B\C\D::f()

### 路由层级
EasySwoole理论上支持无限层级的URL=>控制器映射，但出于系统效率和防止恶意URL访问，
系统默认为3级，若由于业务需求，需要更多层级的URL映射匹配，请于框架初始化事件中，进行对
SysConst::CONTROLLER_MAX_DEPTH值的修改。



