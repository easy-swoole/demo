# 模板引擎
EasySwoole虽说是专为API打造，但难免有些用户想一站全撸，本文以Smarty模板引擎为例，配合Apache或者是Nginx做静态服务器，构建全站开发示例。
## 静态文件处理规则
### Nginx转发规则
```
server {
    root /data/wwwroot/;
    server_name local.swoole.com;
    location / {
        proxy_http_version 1.1;
        proxy_set_header Connection "keep-alive";
        proxy_set_header X-Real-IP $remote_addr;
        if (!-e $request_filename) {
             proxy_pass http://127.0.0.1:9501;
        }
    }
}
```
### Apache转发规则
```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  #RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]  fcgi下无效
  RewriteRule ^(.*)$  http://127.0.0.1:9501/$1 [QSA,P,L]
   #请开启 proxy_mod proxy_http_mod requset_mod
</IfModule>
```

## 支持库引入
下载Smarty，并将其全部项目文件放入App/Vendor/Smarty目录下。在框架初始化事件引入。
```
$loader = AutoLoader::getInstance()->requireFile("App/Vendor/Smarty/Smarty.class.php");
```

## 基础封装
```
namespace App\Utility;


use Core\Component\Di;

class Smarty extends \Smarty
{
    function __construct()
    {
        $tempDir = Di::getInstance()->get(SysConst::TEMP_DIRECTORY);
        parent::__construct();
        $this->setCompileDir("{$tempDir}/templates_c/");
        $this->setCacheDir("{$tempDir}/cache/");
        //注意这里设置了模板目录
        $this->setTemplateDir(ROOT."/App/Static/Template/");
        $this->setCaching(false);
    }
    /*
    封装这个函数的原因在于，smarty的dispaly是直接echo输出的
    */
    function getDisplayString($tpl){
        return $this->fetch($tpl,$cache_id = null, $compile_id = null, $parent = null, $display = false,
            $merge_tpl_vars = true, $no_output_filter = false);
    }
}
```
## 建立自定义控制器抽象类
```
namespace App\Controller\Test;


use App\Utility\Smarty;
use Core\AbstractInterface\AbstractController;

abstract class Base extends AbstractController
{
    function display($tpl,$data = array()){
        $smarty = new Smarty();
        foreach ($data as $key => $item){
            $smarty->assign($key,$item);
        }
        $this->response()->write($smarty->getDisplayString($tpl));
    }
}
```

## 建立测试控制器
```
namespace App\Controller\Test;


class Index extends Base
{

    function index()
    {
        // TODO: Implement index() method.
        $this->display("index.html",array(
           "welcome"=>"easySwoole" 
        ));
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }
}
```
>请注意自己建立模板文件。

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
