视图层模板引擎迁移
------

> 仓库地址: [ThinkTemplate](https://github.com/top-think/think-template)

从ThinkPHP5.1独立出来的编译型模板引擎

- 支持XML标签库和普通标签的混合定义；
- 支持直接使用PHP代码书写；
- 支持文件包含；
- 支持多级标签嵌套；
- 支持布局模板功能；
- 一次编译多次运行，编译和运行效率非常高；
- 模板文件和布局模板更新，自动更新模板缓存；
- 系统变量无需赋值直接输出；
- 支持多维数组的快速输出；
- 支持模板变量的默认值；
- 支持页面代码去除Html空白；
- 支持变量组合调节器和格式化功能；
- 允许定义模板禁用函数和禁用PHP语法；
- 通过标签库方式扩展；

安装
------

```
composer require topthink/think-template
```

添加模板配置
------

这里仅配置必须的配置项，完整配置可以参考本文最下方的配置列表，选择自己需要的添加到配置中，如果还没有创建视图目录`Views`和视图缓存目录`Temp/TplCache`，请提前创建好，并确保缓存目录有写权限

```
private function userConf()
{
	return array(
		'template' => [
			// 模板文件路径
			'view_path'   => ROOT . '/Views/',
			// 模板编译缓存文件夹
			'cache_path'  => ROOT . '/Temp/TplCache/',
			// 模板文件后缀
			'view_suffix' => 'html',
		]
	);
}
```

添加测试模板文件
------
在`View`目录下添加一个模板文件`Index.html`，可以直接复制下面的内容来测试

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

添加View控制器模板
------
为了方便调用，我们创建一个继承自`AbstractController`的模板类，并实现`fetch`方法，需要视图输出的控制器来继承这个控制器，以便快速的输出模板

```
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

```
<?php

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

然后重启服务，访问`http://localhost:9501`看到刚才模板文件中的内容，则说明模板引擎加载成功

完整配置参考
------

```
 // 模板路径
 'view_path'          => '',
 // 默认模板文件后缀
 'view_suffix'        => 'html',
 // 默认模板分隔符
 'view_depr'          => DIRECTORY_SEPARATOR,
 // 模板缓存目录
 'cache_path'         => '',
 // 默认模板缓存后缀
 'cache_suffix'       => 'php', 
 // 模板引擎禁用函数
 'tpl_deny_func_list' => 'echo,exit', 
 // 默认模板引擎是否禁用PHP原生代码
 'tpl_deny_php'       => false, 
 // 模板引擎普通标签开始标记
 'tpl_begin'          => '{', 
 // 模板引擎普通标签结束标记
 'tpl_end'            => '}', 
 // 是否去除模板文件里面的html空格与换行
 'strip_space'        => false, 
 // 是否开启模板编译缓存,设为false则每次都会重新编译
 'tpl_cache'          => true, 
 // 模板编译类型
 'compile_type'       => 'file', 
 // 模板缓存前缀标识，可以动态改变
 'cache_prefix'       => '', 
 // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
 'cache_time'         => 0, 
 // 布局模板开关
 'layout_on'          => false, 
 // 布局模板入口文件
 'layout_name'        => 'layout', 
 // 布局模板的内容替换标识
 'layout_item'        => '{__CONTENT__}', 
 // 标签库标签开始标记
 'taglib_begin'       => '{', 
 // 标签库标签结束标记
 'taglib_end'         => '}', 
 // 是否使用内置标签库之外的其它标签库，默认自动检测
 'taglib_load'        => true, 
 // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
 'taglib_build_in'    => 'cx', 
 // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
 'taglib_pre_load'    => '', 
 // 模板渲染缓存
 'display_cache'      => false, 
 // 模板缓存ID
 'cache_id'           => '', 
 // 模板替换字符串
 'tpl_replace_string' => [],
 // .语法变量识别，array|object|'', 为空时自动识别
 'tpl_var_identify'   => 'array', 
 // 默认过滤方法 用于普通标签输出
 'default_filter'     => 'htmlentities', 
```
