# 微信小程序开发示例

本示例将演示如何使用easyswoole进行小程序开发，使用http web server模式。阅读本教程前，请先完成文档的阅读工作。

## 准备阶段

请先完成 [框架安装](Introduction/install.md) 的步骤。在本实例中，请先运行

```bash
 php easyswoole install
```
安装好运行时环境，安装好的的目录结构如下所示：

project              项目部署目录
----------------------------------
├─Log        日志目录
├─Temp     缓存目录
├─vendor   框架及库目录
├─config.php   配置文件
├─easyswoole  命令行
├─easyswoole.install  命令行安装表示
├─EasySwooleEvent.php 主事件配置文件
----------------------------------

## 配置小程序使用的目录结构

首先创建目录 Application，并在该目录下创建HttpController目录，Utility目录。easywoole用户编写的程序将从Application目录下访问。创建好的目录结构如下
----------------------------------
├─Application     应用程序目录
│  └─HttpController      WEB应用的控制器目录
│  └─Utility                  应用相关工具类目录
├─Log        日志目录
├─Temp     缓存目录
├─vendor   框架及库目录
├─config.php   配置文件
├─easyswoole  命令行
├─easyswoole.install  命令行安装表示
├─EasySwooleEvent.php 主事件配置文件
----------------------------------


## 创建第一个文件

由于框架应用程序目录设置灵活，所以创Application文件夹后，需要在composer.json中进行注册，以后程序即可从该目录下访问文件。

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

执行 `composer dumpautoload` 命令更新命名空间，框架已经可以自动加载 **Application **目录下的文件了，确认以上操作均正确执行。以上内容在安装框架的章节中已经提及，在此只是做一个强调。

接下来在 `HttpController`目录创建建Base.php文件，用来继承easyswoole主控制器，在该文件里，我们将创建几个方法，用来在所有控制器里使用。

```php
<?php
namespace App\HttpController;
use EasySwoole\Config;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\AbstractInterface\Controller;



class Base extends Controller
{




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




    function index()
    {
        parent::index();
    }
}
```

接下来在 `HttpController`目录创建建 Wechat.php文件，该类继承 base.php 控制器，在该文件里，我们将创建微信登陆相关的操作方法。

```php
<?php
namespace App\HttpController;



class Base extends Controller
{

<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/28
 * Time: 上午11:17
 */
namespace App\HttpController;
use App\Utility\Tools;

use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Component\Logger;
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
在Wechat.php类中，我们要实现两个方法 getToken 以及 login。下一节，将介绍小程序登录的流程。


## 小程序登录获取session_key

小程序登录流程需要访问 微信公众平台开发文档 <https://developers.weixin.qq.com/miniprogram/dev/api/api-login.html>

简单说，我们需要使用微信API的两个接口 wx.login与wx.getUserInfo。

wx.login在小程序中调用后会携带参数code，请求我们自己的服务器，服务器通过appid，secret，code请求微信服务器换回session_key，至于这个session_key，的用途将在下一节说明。

如下js请创建一个模板小程序替换APP.js里 onLaunch 的内容。然后刷新，就可以看到请求。

```
//app.js
App({
  onLaunch: function() {
    wx.login({
      success: function(res) {
        if (res.code) {
          //发起网络请求
          wx.request({
            url: 'http://localhost:9501/wechat/getToken',
            data: {
              code: res.code
            }
          })
        } else {
          console.log('登录失败！' + res.errMsg)
        }
      }
    });
  }
})
```

我们可以用一个第三方的微信开发SDK来简化我们与微信服务器的交互，下面打开composer.json，在之前的基础上，require里追加内容``overtrue/wechat": "~4.0``，注意如果是两行内容，第一行json需要增加逗号。

```json
{
  
    "require": {
        "easyswoole/easyswoole": "2.x-dev"，
          "overtrue/wechat": "~4.0"
    }
}
```
继续执行```composer update``` 安装新库。执行完毕后请在```Wechat.php```中增加相关use以便引用


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


	//现在来实现getToken方法。
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

以上，就是通过微信小程序中的请求获取 session_key 的步骤，下一节将说明如何通过 session_key 获取用户信息。


## 小程序登录获取用户资料

换取 session_key 的目的是因为我们登录的时候需要获得微信用户的昵称，头像，openid等信息。这些信息将会保存到数据库以便用户下次登录。然而获取用户信息需要先获取 session_key。

我们需要在 wx.login 调用成功后 再请求 wx.getUserInfo， 成功后 将 wx.login返回的 session_key 带上，加上 wx.getUserInfo 返回的 encryptedData,iv带上，然后请求服务器。

由于返回的数据是加密的，所以我们在服务端要进行解密，这也是login方法要实现的。

```
//app.js
App({
  onLaunch: function() {
    wx.login({
      success: function(res) {
        if (res.code) {
          //发起网络请求
          wx.request({
            url: 'http://localhost:9501/wechat/getToken',
            data: {
              code: res.code
            },
              success: function(res) {
              var session_key = res.data.result.session_key
                   wx.request({
                    	url: 'http://localhost:9501/wechat/login',
                        //注意带有加密信息的参数需要使用POST方法，同时header要设置如下，否者获取不到参数
                          header: {     "Content-Type": "application/x-www-form-urlencoded"    },
                          method: "POST",  
                    	data: {
                          encryptedData: res.encryptedData,
                          session_key:session_key,
                          iv:res.iv
               		 	},
                        success: function(res){
                        	
                        }
              }
          })
        } else {
          console.log('登录失败！' + res.errMsg)
        }
      }
    });
  }
})
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

以上就完成了微信小程序登录的全过程。实际使用时我们需要对返回的openId进行加密以便在客户端传递。目前还没涉及到数据库保存的过程，将在下一章提供。


##  生成访问令牌

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
...
 'ENCRYPT' => [
        'key' => 'aaa',
        'iv' => '注意iv必须是16位的字符串不要多了少了',
    ],
    ....
```

然后我们在wechat.php 增加这个工具类的调用方法 ```use App\Utility\Tools;```

修改login方法的success。

```php
      $this->success(['session_id' => Tools::sessionEncrypt($res['openId'])]);
```

最后在需要验证身份的地方使用```Tools::sessionCheckToken($token)```来验证token有效性。



##  小节

以上就完成了用户登录的过程及会话维持，但是还没涉及数据保存。下一章将配合这个示例完成数据操作的部分。

如果有任何疑问，可以在群里联系我，我会不断更新该示例。
