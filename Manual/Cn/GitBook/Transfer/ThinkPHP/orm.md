数据库与模型迁移
------

> 仓库地址: [ThinkORM](https://github.com/top-think/think-orm)

基于PHP5.6+ 的ORM实现，主要特性：

- 基于ThinkPHP5.1的ORM独立封装
- 保留了绝大部分的ThinkPHP ORM特性
- 支持Db类和模型操作
- 适用于不使用ThinkPHP框架的开发者

独立出来的ORM类相比TP本身增加了几个方法

- setConfig 设置全局配置信息
- getConfig 获取数据库配置信息
- setQuery 设置数据库Query类名称
- setCacheHandler 设置缓存对象Handler（必须支持get、set及rm方法）
- getSqlLog 用于获取当前请求的SQL日志信息（包含连接信息）

安装
------

```
composer require topthink/think-orm
```

添加数据库配置
------

修改`Conf/Config.php`在`userConf`方法中添加如下配置

```
private function userConf()
	{
		return array(
			'database' => [
				// 数据库类型
        		'type'            => '',
        		// 服务器地址
        		'hostname'        => '',
        		// 数据库名
        		'database'        => '',
        		// 用户名
        		'username'        => '',
        		// 密码
        		'password'        => '',
        		// 端口
        		'hostport'        => '',
        		// 连接dsn
        		'dsn'             => '',
        		// 数据库连接参数
        		'params'          => [],
        		// 数据库编码默认采用utf8
        		'charset'         => 'utf8',
        		// 数据库表前缀
        		'prefix'          => '',
        		// 数据库调试模式
        		'debug'           => false,
        		// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        		'deploy'          => 0,
        		// 数据库读写是否分离 主从式有效
        		'rw_separate'     => false,
        		// 读写分离后 主服务器数量
        		'master_num'      => 1,
        		// 指定从服务器序号
        		'slave_no'        => '',
        		// 是否严格检查字段是否存在
        		'fields_strict'   => true,
        		// 数据集返回类型
        		'resultset_type'  => '',
        		// 自动写入时间戳字段
        		'auto_timestamp'  => false,
        		// 时间字段取出后的默认时间格式
        		'datetime_format' => 'Y-m-d H:i:s',
        		// 是否需要进行SQL性能分析
        		'sql_explain'     => false,
        		// Builder类
        		'builder'         => '',
        		// Query类(请勿删除)
        		'query'           => '\\think\\db\\Query',
        		// 是否需要断线重连
        		'break_reconnect' => true,
        		// 数据字段缓存路径
        		'schema_path'     => '',
        		// 模型类后缀
        		'class_suffix'    => false,			]
		);
	}
```

初始化DB配置
------
在`Conf/Event.php`的`框架初始化完成`事件中初始化数据库类配置

```
// 初始化完成
function frameInitialized()
{
	// 初始化数据库
	$dbConf = Config::getInstance()->getConf('database');
	Db::setConfig($dbConf);
}
```

测试集成是否正常
------
数据库初始化完成后即可在控制器内使用，如果需要使用到查询缓存，还需要使用`setCacheHandler`方法添加缓存句柄，后面加入缓存集成的时候会提到，让我们先确认一下Db类是否能正常工作

```
// 在Index控制器类添加以下方法

function index()
{
	$version = Db::query('select version();');
	$this->response()->write($version);
}

```

重启服务后访问`http://localhost:9501`看到数据库的版本，即可正常使用`Db`操作数据库以及使用模型处理业务逻辑，操作方法和TP原生的操作方法基本是一致的，可以参考TP官方的操作手册 [数据库](https://www.kancloud.cn/manual/thinkphp5_1/353998) 以及 [模型操作](https://www.kancloud.cn/manual/thinkphp5_1/354041)

数据库操作
------

数据库操作和TP是一致的，可以使用常规的链式方法查询，如

```
Db::name('table')->where('username','test')->find();
```

也可以直接执行查询语句

```
Db::query("SELECT * FROM users WHERE username='test';")
```

默认返回的是一个数组，如果需要返回数据集也可以在配置文件中添加配置，和TP添加配置是一样的方法，在此就不再赘述

模型操作
------

#### 定义模型
------
定义模型和TP也是类似的操作，为了方便演示操作我们在这里创建一个演示表

```
CREATE TABLE member (
	id int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT COMMENT '用户ID',
	username char(16) NOT NULL DEFAULT '' COMMENT '用户名'
) COMMENT = '会员表';
```

然后在`App\Model`创建对应的模型`Member.php`，具体的创建路径不局限于这个目录，可以根据自己项目需求自己定义，只要能加载到模型类即可

```
<?php

namespace App\Model;

use think\Model;

class Member extends Model
{
    protected $name = 'member';
}
```

然后我们尝试通过模型来访问数据库的数据

```
function index(){
	$member = Member::get(1);
	$member->username = 'test';
	$member->save();
	$this->response()->write('Ok');
}
```

更多使用方法请参考TP官方手册 [数据库](https://www.kancloud.cn/manual/thinkphp5_1/353998) 以及 [模型操作](https://www.kancloud.cn/manual/thinkphp5_1/354041)