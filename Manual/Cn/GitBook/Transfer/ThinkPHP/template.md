# 数据库与模型

------

> Github : [ThinkTemplate](https://github.com/top-think/think-template) - 从ThinkPHP5.1独立出来的编译型模板引擎

安装
------

```bash
composer require topthink/think-template
```

创建模板配置
------

修改 `Conf/Config.php` 文件，在userConf方法中添加如下配置，这里仅配置必须的配置项，完整配置可以参考类库的`think\Template`类，如果还没有创建视图目录Views和视图缓存目录Temp/TplCache，请提前创建好，并确保缓存目录有写权限

```php
private function userConf()
{
  return array(
    'template' => [
      // 模板文件目录
      'view_path'   => './template/',
      // 编译后的模板文件缓存目录
      'cache_path'  => './runtime/',
      // 模板文件后缀
      'view_suffix' => 'html',
    ]
  );
}
```

添加测试模板
------

在`View`目录下添加一个模板文件`Index.html`，可以直接复制下面的内容来测试


```html
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

封装视图控制器
------

为了方便调用，我们创建一个继承自`AbstractController`的模板类，并实现fetch方法，需要视图输出的控制器来继承这个控制器，以便快速的输出模板

```php
<?php

namespace App;

use Conf\Config;
use Core\AbstractInterface\AbstractController;
use think\Template;

abstract class ViewController extends AbstractController
{
    protected function fetch($tplName, $tplData = [])
    {
        $tplConfig = Config::getInstance()->getConf('template');
        $engine = new Template($tplConfig);

        // 由于ThinkPHP的模板引擎是直接echo输出到页面
        // 这里我们打开缓冲区，让模板引擎输出到缓冲区，再获取到模板编译后的字符串

        ob_start();
        $engine->fetch($tplName, $tplData);
        $content = ob_get_clean();
        $this->response()->write($content);
    }
}
```

添加测试控制器
------

修改默认的Index控制器，位于`App\Controller\Index.php`，继承自`ViewController`并尝试输出模板

```php
namespace App\Controller;

use App\ViewController;

class Index extends ViewController
{

    function index()
    {
        // 输出Index模板
        $this->fetch('Index');
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

至此已经可以将`ThinkPHP`的模板文件直接迁移到`easySwoole`中，更多模板的用法可以参考5.1完全开发手册的[模板引擎](https://www.kancloud.cn/manual/thinkphp5_1/354069)章节