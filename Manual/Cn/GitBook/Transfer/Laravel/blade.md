迁移Blade视图层模板
------

> 仓库地址: [Blade](https://github.com/jenssegers/blade)


安装
------

```
composer require jenssegers/blade
```

通过向其传递视图文件所在的文件夹和缓存文件夹来创建一个Blade实例。通过调用make方法来渲染一个模板。有关Blade模板引擎的更多信息可以在http://laravel.com/docs/5.1/blade
添加模板配置

------
先单例Blade,为什么我们要用单例？先留个彩蛋
```
namespace App\Vendor\Db;
class Blade
{

    static function getInstance(){
        static $blade = null;
        if($blade === null){
            $blade = new \Jenssegers\Blade\Blade([ROOT . '/views/'],ROOT . '/Temp/TplCache');
        }
        return $blade;
    }

}

```

建立视图控制器抽象类
```
<?php

namespace App\Controller;

use Core\AbstractInterface\AbstractController;
use App\Vendor\Db\Blade;

abstract class ViewController extends AbstractController
{

    function View($tplName, $tplData = [])
    {
        $viewTemplate = Blade::getInstance()->render($tplName, $tplData);
        $this->response()->write($viewTemplate);
    }
}
```

添加测试模板文件
------
在`views`目录下添加一个模板文件`Index/index.balde.php`，可以直接复制下面的内容来测试

```
<!doctype html>
<html lang="zh">
<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Template Test</title>
</head>
<body>
<p>If you see this message, the template engine has been initialized successfully</p>
</body>
</html>
```


添加测试控制器
------
修改默认的Index控制器，位于`App\Controller\Index.php`，继承自`ViewController`并尝试输出模板

```
<?php

namespace App\Controller;

use App\ViewController;

class Index extends ViewController
{

    function index()
    {
        // 输出Index模板
        $this->View('Index/index', ['name' => 'easySwoole']);
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

然后重启服务，访问`http://localhost:9501`看到刚才模板文件中的内容，则说明模板引擎加载成功

使用拓展 Blade(彩蛋在这里)
-----

通过调用compiler函数轻松创建指令
```
use App\Vendor\Db\Blade;

//需要在server启动前写
Blade::getInstance()->compiler()->directive('datetime', function ($expression) {
    return "<?php echo with({$expression})->format('F d, Y g:i a'); ?>";
});

//在你的模板内
<?php $dateObj = new DateTime('2017-01-01 23:59:59') ?>
@datetime($dateObj)
```

由于不能实现 Larvel 服务容器 中检索服务，有些功能是不能实现的，如 服务注入, 共享视图模板，自定义 If 语句

题外扩展
---

es本身不提供帮助函数,我们可以创建Conf/Helpers.php,然后
```
//服务启动前加载，我们的blade就可以使用自定义的函数了
$loader->requireFile('Conf/Helpers.php');
```

UP主扩展了几个方法，若使用不当的可以在群里指道一下UP主
```
use Illuminate\Support\HtmlString;

if (! function_exists('app')) {
    /**
     * 获取Di容器
     *
     * @param  string  $abstract
     * @return mixed|\Core\Component\Di
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return \Core\Component\Di::getInstance();
        }

        return \Core\Component\Di::getInstance()->get($abstract);
    }
}
if (! function_exists('asset')){
    /**
     * 转发静态文件
     *      \Conf\Config::getInstance()->getConf('ForwardingDomain')  转发域名
     * @param string $path 静态文件路径
     * @return string 
     */
    function asset($path = ''){
        return \Conf\Config::getInstance()->getConf('ForwardingDomain') .'/'. $path;
    }
}

if (! function_exists('url')){
    /**
     * @param $path 基本的url跳转
     * @return string
     */
    function url($path){
        return 'http://' . \Core\Http\Request::getInstance()->getHeader('host')[0] . '/' . $path;
    }
}

if (! function_exists('currentUrl')){
    /**
     * 获取当前的url
     * @return \Core\Http\Message\Uri
     */
    function currentUrl(){
        return \Core\Http\Request::getInstance()->getUri();
    }
}

if (! function_exists('method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb used by forms.
     *
     * @param  string  $method
     * @return \Illuminate\Support\HtmlString
     */
    function method_field($method)
    {
        return new HtmlString('<input type="hidden" name="_method" value="'.$method.'">');
    }
}

```

