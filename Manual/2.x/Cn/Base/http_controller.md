# URL与控制器
## 控制器
EasySwoole中,默认为App\HttpController，如需修改，可以在全局的mainServerCreate事件中，重新注册registerDefaultOnRequest方法。
所有的控制器都应继承自EasySwoole\Core\Http\AbstractInterface\Controller。
### Controller中的自带方法
- index()
    
  控制器中默认存在方法，当在URL中无法解析出actionName时，将默认执行该方法。例如有一个Test控制器，当访问domain/test路径时，则默认解析为index。

- actionNotFound($action)
  
  当一个URL请求进来，能够被映射到控制器且做完actionName解析后，将立马执行OnRequest事件，以便对请求做预处理，如权限过滤等。注意，该事件与Conf/Event下的onRequest事件并不冲突（Conf/Event优先级最高）。
  
- afterAction($actionName)

  当在URL中解析出actionName，而在控制器中无存在对应方法（函数）时，则执行该方法。例如有一个Test控制器，当访问domain/test/test1/index.html路径时，actionName会被解析为test1，而此时若控制器中无test1方法时，则执行actionNotFount。
- onException(\Throwable $throwable,$actionName)
  
  在任何的控制器响应结束后，均会执行该事件,该事件预留于做分析记录。
  
- onRequest($action)  
- getActionName()
- resetAction(string $action)
- request()
- response()
- writeJson($statusCode = 200,$result = null,$msg = null)
- validateParams(Rules $rules)
### 关于AbstractController中的实体方法
- actionName()
  
  当一个URL请求进来，能够被映射到控制器时，那么将从该URL中解析出对应的行为名称，若无则默认为index。在控制器内的任意位置调用$this->actionName()均能获得当前行为名称。
  
- request()

  返回当前Core\Http\Request实例。
- response()
    
  返回当前Core\Http\Response。
  
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>  
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
 
  

    