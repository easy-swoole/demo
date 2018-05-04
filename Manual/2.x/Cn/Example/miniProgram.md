# 微信小程序开发示例

本示例将演示如何使用 `easyswoole` 进行小程序开发，使用 http web server 模式。阅读本教程前，请先完成文档的阅读工作。

# 第一章 控制器、方法与请求
## 准备阶段

请先完成 [框架安装](Introduction/install.md) 的步骤。在本示例中，请先运行 `php easyswoole install`

安装好运行时环境，安装好的的目录结构如下所示：

```
project              项目部署目录
----------------------------------
├─Log          日志目录
├─Temp         缓存目录
├─vendor       框架及库目录
├─config.php   配置文件
├─easyswoole   命令行
├─easyswoole.install  命令行安装表示
├─EasySwooleEvent.php 主事件配置文件
----------------------------------
```

## 配置小程序使用的目录结构

首先创建 `Application` 目录，并在该目录下创建 `HttpController` 、`Utility` 目录。`easywoole` 用户编写的程序将从 `Application` 目录下访问。创建好的目录结构如下：


```
project              项目部署目录
----------------------------------
├─Application         应用程序目录
│  └─HttpController   WEB应用的控制器目录
│  └─Utility          应用相关工具类目录
├─Log          日志目录
├─Temp         缓存目录
├─vendor       框架及库目录
├─config.php   配置文件
├─easyswoole   命令行
├─easyswoole.install  命令行安装表示
├─EasySwooleEvent.php 主事件配置文件
----------------------------------
```

## 创建首个文件

由于框架应用程序目录设置灵活，所以创建 `Application` 文件夹后，需要在 `composer.json` 中进行注册，以后程序即可从该目录下执行相关文件。

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "Application/"
        }
    },
    "require": {
        "easyswoole/easyswoole": "2.x-dev"
    }
}
```

执行 `composer dumpautoload` 命令更新命名空间，框架就可以自动加载 `Application` 目录下的文件了，确认以上操作均正确执行。以上内容在安装框架的章节中已经提及，在此只是做一个强调。

接下来在 `HttpController` 目录创建 `Base.php` 文件，用来继承 `easyswoole` 主控制器，在该文件里，我们将创建几个方法用来在所有控制器里使用。

```php
<?php
namespace App\HttpController;
use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

class Base extends Controller
{

	//用来返回错误信息（json）
    function error($code, $message){
        if(!$this->response()->isEndResponse()){
            $data = Array(
                "code"   => $code ,
                "result" => "",
                "msg"    => $message
            );
            $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }

   //用来返回成功信息（json）
    function success($result = '', $code=0){
        if(!$this->response()->isEndResponse()){
            $data = Array(
                "code"=>$code,
                "result"=>$result
            );
            $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus(200);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }


	// controller 类必须实现该抽象方法，不然会报错
    function index()
    {
        parent::index();
    }
}
```

接下来在 `HttpController` 目录创建建 `Wechat.php` 文件，该类继承 `base.php` 控制器，在该文件里，我们将创建微信登陆相关的操作方法。

```php
<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/28
 * Time: 上午11:17
 */
namespace App\HttpController;

use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;

use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;


class Wechat extends Base
{

    function index()
    {
        $this->response()->write('hello world');
    }
    
     protected function getMiniProgramConfig(){
        return [
            'app_id' => 'appid',
            'secret' => 'appsecret',
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => Config::getInstance()->getConf('LOG_DIR').'/wechat.log',
            ],
        ];
    }
    
     function getToken(){ }


    function login(){ }

}
```
在 `Wechat.php` 类中，我们要实现两个方法 `getToken` 以及 `login`。下一节，将介绍小程序登录的流程。


## 小程序登录获取session_key

小程序登录流程需要访问 微信公众平台开发文档 <https://developers.weixin.qq.com/miniprogram/dev/api/api-login.html>

简单说，我们需要使用微信小程序的两个接口 wx.login、wx.getUserInfo。

wx.login在小程序中调用后会携带参数code，请求我们自己的服务器，服务器通过 `appid` `secret` `code`请求微信服务器换回 `session_key`，至于这个 `session_key` 的用途将在下一节说明。

如下js请创建一个模板小程序替换APP.js里 onLaunch 的内容。然后刷新，就可以看到请求。

```
//app.js
//app.js
App({
  onLaunch: function () {
  
    wx.login({
      success: function (res) {
        //发起网络请求
        wx.request({
          url: 'http://localhost:9501/wechat/getToken',
          data: {
            code: res.code,
          },
          success: function(res){
          //获取session_key 由于微信自己封装了一层data，所以我们需要使用res.data.result才能回去接口返回对的result
            var session_key = res.data.result.session_key;
            //todo 下面准备第二个请求
          }
        })
      }
    });

  }
})
```
接下来看服务端

我们可以用一个第三方的微信开发SDK来简化我们与微信服务器的交互，在工作目录执行命令 `composer require overtrue/wechat:~4.0` 安装`easywechat`，或者
打开`composer.json` 在之前的`require`基础上追加内容 `overtrue/wechat": "~4.0` 注意如果是两行内容，第一行json需要增加逗号。

```json
{
  
    "require": {
        "easyswoole/easyswoole": "2.x-dev",
          "overtrue/wechat": "~4.0"
    }
}
```
继续执行`composer update` 安装新库。执行完毕后请在 `Wechat.php` 中增加相关use以便引用

```php
<?php
//增加如下USE
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\DecryptException;

class Wechat extends Base
{


	//现在来实现getToken方法
    function getToken(){
    	//从小程序端接受code
        $code = $this->request()->getQueryParam('code');
        //初始化EasyWeChat，设置配置文件（也可以写在config.php中）这里为了方便就直接写在类里
        $app = Factory::miniProgram($this->getMiniProgramConfig());
        try {
        	//执行外部请求，将从微信服务器获取 session_key，注意目前这个是同步操作
            $ret = $app->auth->session($code);
            if(!isset($ret['session_key'])){
                logger::getInstance()->log('微信session_key获取失败:('.$ret['errcode'].')'.$ret['errmsg']);
                throw new \Exception('系统繁忙，请稍后再试', 101);
            }
            //返回成功后将 session_key 回传给小程序，以便执行第二阶段。
            $this->success($ret);
        }catch (\Exception $e){
            $this->error($e->getCode(),$e->getMessage());
        }
    }


}
```

以上，就是通过微信小程序中的请求获取 `session_key` 的步骤，下一节将说明如何通过 `session_key` 获取用户信息。


## 小程序登录获取用户资料

换取 `session_key` 的目的是因为我们登录的时候需要获得微信用户的昵称、头像、openid信息。这些信息将会保存到数据库以便用户下次登录，然而获取用户信息需要先获取 `session_key`。

我们需要在 `wx.login` 调用成功后 再请求 `wx.getUserInfo`， 成功后将 `wx.login` 返回的 `session_key` 带上，加上 `wx.getUserInfo` 返回的 `encryptedData` `iv` 一起带上，然后请求服务器。

由于返回的数据是加密的，所以我们在服务端要进行解密，这也是 `login` 方法要实现的。

```
app.js
wx.login({
      success: function (res) {


        //发起网络请求
        wx.request({
          url: 'http://localhost:9501/wechat/getToken',
          data: {
            code: res.code,
          },
          success: function(res){
            var session_key = res.data.result.session_key;
            //下面是我们调用该方法获得加密信息以及iv
            wx.getUserInfo({
              success: function (res) {
                console.log(res);

                //发起网络请求
                wx.request({
                //使用POST方法发送
                  method: 'POST',
                  url: 'http://localhost:9501/wechat/login',
                  //POST方法要加该header头，否则POST过去没有参数
                  header: {
                    "Content-Type": "application/x-www-form-urlencoded",
                  },
                  data: {
                    //这个 session_key 则是 wx.login 登录成功后请求我们getToken返回的
                    session_key: session_key,
                    encryptedData: res.encryptedData,
                    iv: res.iv,
                    signature: res.signature
                  }, success: function (res) {
                    console.log(res);
                  }
                })

              }
            });
            
          }
        })

      }
    });
```

现在看下login方法的实现


```php
<?php

use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;

//增加如下USE
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\DecryptException;


class Wechat extends Base
{

    
	function login(){
            $app = Factory::miniProgram($this->getMiniProgramConfig());
            //获取POST的参数
            $args = $this->request()->getParsedBody();

            try{
            //解密用户信息
                $res =  $app->encryptor->decryptData($args['session_key'], $args['iv'], $args['encryptedData']);
                if(!$res){
                    $this->error(105,'获取用户信息失败，请稍后再试');
                }
                //解密成功返回openid （这只是demo，下一节将加入加密解密用户token）
                $this->success($res['openId']);

			//接一下解密异常的exception
            }catch(DecryptException $e){
                $this->error(102, '解密数据错误,请重新登录');
            }catch (\Exception $e){
                $this->error($e->getCode(), $e->getMessage());
            }
    }


}
```

以上就完成了微信小程序登录的全过程。实际使用时我们需要对 `success`方法返回的 `openId` 进行加密以便在客户端传递。目前还没涉及到数据库保存的过程，将在下一章提供。


##  生成访问Token

在 ```Utility```目录下创建 ```Tools.php```文件，用来存放加密解密方法。


```php
<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/28
 * Time: 下午5:19
 */

namespace App\Utility;
use EasySwoole\Config;
use EasySwoole\Core\Component\Logger;

class Tools{
    /**
     * 加解密
     * @param $data
     * @return string
     */
    public static function decryptWithOpenssl($data){
        $key = Config::getInstance()->getConf('ENCRYPT.key');
        $iv = Config::getInstance()->getConf('ENCRYPT.iv');
        return openssl_decrypt(base64_decode($data),"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv);
    }

    public static function encryptWithOpenssl($data){
        $key = Config::getInstance()->getConf('ENCRYPT.key');
        $iv = Config::getInstance()->getConf('ENCRYPT.iv');
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv));
    }


    /**构建会话加密函数，默认30天超时
     * @param $openid
     * @param int $exptime
     * @return string
     */
    public static function sessionEncrypt($openid, $exptime=2592000){
        $exptime = time() + $exptime;
        return self::encryptWithOpenssl($openid.'|'.$exptime);
    }

    /**
     * 验证会话token是否有效
     * @param $raw
     * @return bool
     */
    public static function sessionCheckToken($raw){

        //如果解密不出文本返回失败
        if(!$data = self::decryptWithOpenssl($raw)){
            Logger::getInstance()->console('解密不出文本');
            return false;
        }

        Logger::getInstance()->console($data);
        $token = explode('|', $data);
        //如果分离出来的openid或者exptime为空 返回失败
        if(!isset($token[0]) || !isset($token[1])){
            Logger::getInstance()->console('分离不出openid exptime');
            return false;
        }
        //如果时间过期，返回失败
        if( $token[1] < time()){
            Logger::getInstance()->console('时间过期于：' . date('Y-m-d', $token[1] ));
            return false;
        }

        return true;
    }
}
```
在配置文件中增加加密解密使用的密钥


```php
<?php
return [
    //部分配置
 'ENCRYPT' => [
        'key' => 'aaa',
        'iv' => '注意iv必须是16位的字符串不要多了少了',
    ],
];
```

然后我们在 `vwechat.php` 增加这个工具类的调用方法 `use App\Utility\Tools;`

修改 `login` 方法的 `success`为下列内容。

```php
      $this->success(['session_id' => Tools::sessionEncrypt($res['openId'])]);
```

最后在需要验证身份的地方使用 `Tools::sessionCheckToken($token)` 来验证令牌有效性。



##  小节

以上就完成了用户登录的过程及会话维持，但是还没涉及数据保存。下一章将配合这个示例完成数据操作的部分。

最后是 `Wechat.php` 全部实现的代码。
```php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/28
 * Time: 上午11:17
 */
namespace App\HttpController;


use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Component\Logger;

use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Http\Request;
use EasySwoole\Core\Http\Response;


use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use App\Utility\Tools;


class Wechat extends Base
{

    function index()
    {
        $this->response()->write('hello world');
    }


    protected function getMiniProgramConfig(){
        return [
            'app_id' => 'wxf921803dd497f1d9',
            'secret' => 'f4d61f43a6ceb08897f7921f4215fa49',
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => Config::getInstance()->getConf('LOG_DIR').'/wechat.log',
            ],
        ];
    }

    function getToken(){
        $code = $this->request()->getQueryParam('code');
        $app = Factory::miniProgram($this->getMiniProgramConfig());
        try {
            $ret = $app->auth->session($code);
            if(!isset($ret['session_key'])){
                logger::getInstance()->log('微信session_key获取失败:('.$ret['errcode'].')'.$ret['errmsg']);
                throw new \Exception('系统繁忙，请稍后再试', 101);
            }
            $this->success($ret);
        }catch (\Exception $e){
            $this->error($e->getCode(),$e->getMessage());
        }
    }


    function login(){
            $app = Factory::miniProgram($this->getMiniProgramConfig());
            $args = $this->request()->getParsedBody();

            try{
                $res =  $app->encryptor->decryptData($args['session_key'], $args['iv'], $args['encryptedData']);
                if(!$res){
                    $this->error(105,'获取用户信息失败，请稍后再试');
                }
                print_r($res);
                $this->success(['session_id' => Tools::sessionEncrypt($res['openId'])]);

            }catch(DecryptException $e){
                $this->error(102, '解密数据错误,请重新登录');
            }catch (\Exception $e){
                $this->error($e->getCode(), $e->getMessage());
            }
    }

    function check(){
        $header = $this->request()->getHeaders();
        print_r($header);
        if(!isset($header['authorization'])){
            $this->error(103,'access denied');
        }

        list ($bearer, $token) = explode(' ',$header['authorization'][0]);

        if(!$token){
            $this->error(104,'token error');
        }

        if(Tools::sessionCheckToken($token)){
            $this->success();
        }else{
            $this->error(106,'check token error');
        }
    }


}
```
# 第二章 数据库交互


## 创建数据模型

在第一章演示了如何创建控制器，如何请求微信小程序的接口获取用户信息，但是还没有执行数据库保存的操作，本章示例将演示如何创建数据模型来进行数据保存。

本示例使用了文档中提供的 `MysqliDb` 库，下面先进行安装。

安装方法：

```bash

composer require joshcam/mysqli-database-class:dev-master

```
或者修改 `composer.json` 文件，在对应的结构中添加如下内容，然后执行 `composer update` 下载库文件并自动创建加载

```
"autoload": {
       "psr-4": {
           "MysqliDb" : "App/Vendor/Db/MysqliDb.php"
       }
   }
```


接下来对配置文件进行扩充，需要增加 Mysql 的配置文件：
```php

<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/12/30
 * Time: 下午10:59
 */
return [
   //...
    'MYSQL'=>[
        'host'     => '127.0.0.1',
        'username' => 'root',
        'password' => '123456',
        'db'       => 'test',
        'port'     => 3306,
        'charset'  => 'utf8',
        'trace'   => true,
    ]
    //...
];
```


然后在 `Application/Utility` 文件夹创建文件 `Db.php` 用来对数据库相关的操作做初始化，请看代码：


```php
<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/29
 * Time: 下午3:30
 */
namespace App\Utility;
//加载配置文件
use EasySwoole\Config;


class Db
{
    
    private $db;
    function __construct()
    {
        //读取配置文件
        
        if(!$this->db = Di::getInstance()->get('MYSQL')){
            $config = Config::getInstance()->getConf('MYSQL');
            $this->db = Di::getInstance()->set('MYSQL',\MysqliDb::class, $config);
            $this->db->setTrace($config['trace']);
            
            //如果要添加主从配置可以使用下面方法继续添加配置
            //$this->db->addConnection('slave', $c);
        }

        
        
    }
    //返回实例化的对象
    function link()
    {
        return $this->db;
    }
}
```

下一步我们需要创建模型，并且让模型继承该类以便直接使用数据库连接。

在 `Application` 目录下创建 `Model` 目录，或根据自己喜好创建存放模型的对应目录。

在 `Model` 目录创建 `Profile.php` 文件，用来保存用户信息。

```php
<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/29
 * Time: 下午3:33
 */
namespace App\Model;
use EasySwoole\Core\Component\Di;
Use EasySwoole\Core\Component\logger;

//需要在这里引入Db类，它负责创建数据库的连接和执行一些通用的数据库操作
use App\Utility\Db;

class Profile extends Db
{
    //设置表名称
    private  $table = 'profile';

    //获取或新增数据，这里我们传了两个参数 openid 和 data， data是微信接口返回的我们解密后的数据
    function getOrInsert($openid, $data){
        
        if($uid = $this->getUserByOpenID($openid)){
            logger::getInstance()->console('getUserByOpenID : ' . $uid);
            return $uid;
        }


        //格式化需要插入的数据
        $row = $this->buildInsertData($data);
        //获取数据库连接，并执行插入，如果插入成功则返回自增id，没有自增id返回true。
        $insertId = $this->link()->insert($this->table, $row);
        logger::getInstance()->console('getOrInsert : ' . $insertId);
        return $insertId;
    }

    //检查openid 是否已经创建，如果创建直接返回ui都给用户
    private function getUserByOpenID($openid){
        logger::getInstance()->console('getUserByOpenID : ' . $openid);
        $res = $this->link()->where ('openId', $openid)->get($this->table, null, 'uid');
        return !empty($res) ?: $res['uid'];
    }

    //格式化一下要插入的数据不要让不存在的字段加入数组
    private function buildInsertData($params){
        return [
            'openId'    => $params['openId'],
            'nickName'  => $params['nickName'],
            'gender'    => $params['gender'],
            'language'  => $params['language'],
            'city'      => $params['city'],
            'province'  => $params['province'],
            'country'   => $params['country'],
            'avatarUrl' => $params['avatarUrl'],
        ];
    }
}
```

最后我们需要修改一下之前 `HttpController` 目录下文件 `Wechat.php` 的 `login` 方法。



```php

    //找到
    $this->success(['session_id' => Tools::sessionTokenBuild($res['openId'])]);
    
    
    //替换成
    if( $uid = (new Profile())->getOrInsert($res['openId'], $res)){
        $this->success(['session_id' => Tools::sessionTokenBuild($uid)]);
    }
```


数据表：

```sql
-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2018-03-30 02:47:14
-- 服务器版本： 5.7.17
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
--
-- 表的结构 `profile`
--

CREATE TABLE `profile` (
  `uid` int(10) NOT NULL,
  `openId` varchar(50) NOT NULL,
  `nickName` varchar(50) DEFAULT NULL,
  `gender` tinyint(1) NOT NULL,
  `language` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  `avatarUrl` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息';

--
-- 转存表中的数据 `profile`
--

INSERT INTO `profile` (`uid`, `openId`, `nickName`, `gender`, `language`, `city`, `province`, `country`, `avatarUrl`) VALUES
(1, 'oKc4D5vJFxJZd7CDuPLcDoeq-W2s', '幻之羽翼', 1, 'zh_CN', 'Chaoyang', 'Beijing', 'China', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqJKSzUQ5nlbna7910sL06Ea7UgcK5iaUl95hlucibhic5LuP1SQPYEcF23KmvToU3a2HLzXOhibcuPdA/0');


--
-- Indexes for dumped tables
--

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `open_id` (`openId`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_public` (`is_public`),
  ADD KEY `title` (`title`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `profile`
--
ALTER TABLE `profile`
  MODIFY `uid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
```

OK，看起来我们成功的完成了数据库的保存，本章示例到此就结束了。


如果有任何疑问，可以在群里联系幻の羽翼，我会收集大家的疑问不断更新该示例。