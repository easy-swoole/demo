## Laravel ORM Eloquent

使用时请注意长连接的[异常处理](Base/exception.md)，否则会出现 PDO::prepare():MySQL server has gone away

### 安装

```json
composer require illuminate/database
```

### 注意

> 有个问题就是安装了illuminate/database不能分页，需要安装illuminate/pagination; illuminate/pagination有个坑，在分页另说

## 添加数据库配置

在 `\EasySwoole\Config` 中添加配置信息：

```Json
'database' => [ 
     'driver'    => 'mysql',
     'host'      => '',
     'database'  => '',
     'username'  => '',
     'password'  => '',
     'charset'   => 'utf8',
     'collation' => 'utf8_general_ci',
     'prefix'    => ''
 ]
       
```

## 初始化DB配置

在 `\EasySwoole\EasySwooleEvent` 的 `框架初始化完成 ` 事件中初始化数据库类配置

```Php
use Illuminate\Database\Capsule\Manager as Capsule;//如果你不喜欢这个名称，as DB;就好 
// 初始化完成
function frameInitialized()
{
    // 初始化数据库
    $dbConf = Config::getInstance()->getConf('database');
    $capsule = new Capsule;
    // 创建链接
    $capsule->addConnection($dbConf);
    // 设置全局静态可访问
    $capsule->setAsGlobal(); 
    // 启动Eloquent
    $capsule->bootEloquent();
}

```

## 测试集成是否正常

数据库初始化完成后即可在控制器内使用，让我们先确认一下Eloquent是否能正常工作

```Php
// 在Index控制器类添加以下方法
function index()
{
    $version = Capsule::select('select version();');
    $this->response()->write($version);
}

```

重启服务后访问`http://localhost:9501`看到数据库的版本，即可正常使用`Capsule`操作数据库以及使用模型处理业务逻辑，操作方法和laravel原生的操作方法基本是一致的，可以参考laravel官方的操作手册 [Eloquent ORM 中文文档](http://laravel-china.org/docs/eloquent)

## 数据库操作

数据库操作和Laravel是一致的，主要由Capsule访问数据库的，可以使用常规的链式方法查询，如

```Php
$users = Capsule::table('users')->where('votes', '>', 100)->get();
```

也可以直接执行查询语句

```Php
$results = Capsule::select('select * from users where id = ?', array(1));
```

默认返回的是一个数组，如果需要返回数据集也可以在配置文件中添加配置，和TP添加配置是一样的方法，在此就不再赘述

## 模型操作

#### 使用Eloquent的结构生成器创建数据库的表

------

创建table.php文件

```Php
<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('email')->unique();
    $table->timestamps();
});

```

然后我们运行table.php，命令行运行：

```php
php table.php
```

也可以在控制器下建表

```php
use Illuminate\Database\Capsule\Manager as Capsule;
// 在Index控制器类添加以下方法
function index()
{
    Capsule::schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('email')->unique();
        $table->timestamps();
    });
}

```

重启服务后访问`http://localhost:9501`,然后我们的查看MySQL数据库里就会有一个users表了

#### 使用模型

------

```Php
use  Illuminate\Database\Eloquent\Model  as Eloquent; 

class User extends  Eloquent 
{
    protected $table = 'users';
}

```

然后我们可以很方便的像在Laravel框架里一样使用Eloquent了：

```php
function index(){
    // 查询id为2的
    $users = User::find(2);

    // 查询全部
    $users = User::all();

    // 创建数据
    $user = new User;
    $user->username = 'someone';
    $user->email = 'some@xxx.com';
    $user->save();
}
```

更多关于Eloquent的使用请参考 [Eloquent ORM 中文文档](http://laravel-china.org/docs/eloquent)

#### 其他著名 ORM

还有很多著名的 ORM 和 Datamapping（数据库迁移等） 包，参见： [ORM and Datamapping](https://github.com/ziadoz/awesome-php#orm-and-datamapping)