# think ORM


> Github : [ThinkORM](https://github.com/top-think/think-orm) - 从ThinkPHP5.1独立出来的数据库ORM类库

## 安装

```Json
composer require topthink/think-orm
```

## 创建数据库配置

在 ` \EasySwoole\Config` 里添加配置项 ，这里仅列出连接`mysql`必须的配置项，完整配置项可以参考`think-orm`类库目录下的`config.php`文件

```php
[
      'database' => [
        // 数据库类型
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        => '127.0.0.1',
        // 数据库名
        'database'        => 'swoole',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => '123456',
        // 端口
        'hostport'        => '3306',
        // 数据库表前缀
        'prefix'          => 'db_',
        // 是否需要断线重连
        'break_reconnect' => true,
      ]
 ]
```

## 全局初始化DB类

在 `\EasySwoole\EasySwooleEvent` 的 `框架初始化完成 ` 事件中初始化数据库类配置，初始化完成后，即可在全局任意位置使用Db类以及使用模型类进行操作

```Php
function frameInitialized()
{
    // 获得数据库配置
    $dbConf = Config::getInstance()->getConf('database');
    // 全局初始化
    Db::setConfig($dbConf);
}
```

## 普通查询示例

和`ThinkPHP`的使用方法一样，可以直接使用Db类进行数据库查询，支持链式操作

```Php
Db::table('user')
    ->data(['name'=>'thinkphp','email'=>'thinkphp@qq.com'])
    ->insert();    
Db::table('user')->find();
Db::table('user')
    ->where('id','>',10)
    ->order('id','desc')
    ->limit(10)
    ->select();
Db::table('user')
    ->where('id',10)
    ->update(['name'=>'test']);    
Db::table('user')
    ->where('id',10)
    ->delete();

```

## 模型查询示例

简单的查询模型无需改动任何代码即可直接应用在EasySwoole中，只需要将原项目的模型文件复制过来，批量修改命名空间，对应为EasySwoole的命名空间即可

同样的我们需要新建一个模型才能进行查询，模型直接继承自`think\Model`类，和`ThinkPHP`的定义方法是一样的

```Php
<?php

namespace App\Model;

use think\Model;

class Member extends Model
{
    protected $name = 'member';
}

```

定义完模型类后，即可进行模型的查询，同样也支持关联查询的定义

```Php
use App\Model\Member;

function index(){
    $member = Member::get(1);
    $member->username = 'test';
    $member->save();
    $this->response()->write('Ok');
}

```

更多模型用法可以参考5.1完全开发手册的[模型](https://www.kancloud.cn/manual/thinkphp5_1/354041)章节