模板引擎
=======
EasySwoole虽说是专为API打造，但难免有些用户想一站全撸，本例介绍了如何集成模板引擎，配合Apache或者是Nginx做静态服务器，构建全站开发示例。

本示例介绍两种模板引擎的集成，分别是[Smarty](#Smarty)引擎和来自`Laravel`的[Blade](#Blade)引擎

集成前准备
------

由于`swoole_http_server`对Http协议的支持并不完整，建议仅将`easySwoole`作为后端服务，并且在前端增加`Nginx`或者`Apache`作为代理，参照下面的例子添加转发规则，将请求转发给`Swoole Server`处理

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

<span id="Smarty">集成Smarty引擎</span>
------

### 支持库引入

下载Smarty，并将其全部项目文件放入App/Vendor/Smarty目录下。在框架初始化事件引入。

```
$loader = AutoLoader::getInstance()->requireFile("App/Vendor/Smarty/Smarty.class.php");
```

### 基础封装

请注意下面的代码中设置了模板目录，请提前新建好模板目录并添加模版文件

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

### 建立自定义控制器抽象类

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

### 建立测试控制器

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

<span id="Blade">集成Blade引擎</span>
------

对于从`Laravel`迁移到`easySwoole`的用户，可以选择集成熟悉的`Blade`引擎，以便快速上手，我们通过`Composer`进行集成以简化集成的难度，如果还没有为`easySwoole`添加`Composer`支持，请参照手册中的`自动加载`章节来配置，也可以用下面的方法集成任意一个通过`Composer`加载的第三方模板引擎

### 安装引擎

```
composer require jenssegers/blade
```

### 建立视图控制器抽象类
```
<?php

namespace App\Controller;

use Core\AbstractInterface\AbstractController;
use Jenssegers\Blade\Blade;

abstract class ViewController extends AbstractController
{
    protected $TemplateViews = ROOT . '/Templates/';
    protected $TemplateCache = ROOT . '/Temp/TplCache';

    function View($tplName, $tplData)
    {
        $blade = new Blade([$this->TemplateViews], $this->TemplateCache);
        $viewTemplate = $blade->render($tplName, $tplData);
        $this->response()->write($viewTemplate);
    }
}
```

### 在控制器中使用

控制器需要继承自`ViewController`,需要提前创建好模板文件，模板文件和laravel是一致的，如下面的例子模板文件是`Templates/Index/index.blade.php`

```
$this->View('Index/index', ['name' => 'easySwoole']);
```

关于`Blade`引擎的使用可以参考`laravel`文档: [http://laravel.com/docs/5.1/blade](http://laravel.com/docs/5.1/blade)

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
