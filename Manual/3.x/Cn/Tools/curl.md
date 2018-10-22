## Curl

#### 命名空间地址

EasySwoole\Curl\Request

#### 方法列表

初始化：

- string `url` 请求地址

```php
function __construct(string $url = null)
```

设置请求地址：

- string `url` 请求地址

```php
public function setUrl(string $url):Request
```

添加Cookie：

- EasySwoole\Curl\Cookie `cookie` 

```php
public function addCookie(Cookie $cookie):EasySwoole\Curl\Request
```

添加POST参数：

- EasySwoole\Curl\Field `field`
- bool `isFile ` 是否为文件

```php
public function addPost(Field $field,$isFile = false):EasySwoole\Curl\Request
```

添加GET参数：

- EasySwoole\Curl\Field `field`

```php
public function addGet(Field $field):EasySwoole\Curl\Request
```

添加用户信息：

- array `opt` 用户信息
- bool `isMerge` 是否合并

> 如：opt 可以设置头部信息

```php
public function setUserOpt(array $opt,$isMerge = true):EasySwoole\Curl\Request
```

执行请求：

```php
public function exec():EasySwoole\Curl\Response
```

获得用户信息：

```php
public function getOpt():array
```



## 自定义封装示例

为了方便自己习惯再封装一下自己喜欢的套路，以下仅为示例代码：

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-16
 * Time: 下午2:16
 */

namespace App\Utility;


use EasySwoole\Curl\Field;
use EasySwoole\Curl\Request;
use EasySwoole\Curl\Response;

class Curl
{
    public function __construct()
    {

    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @return Response
     */
    public function request(string $method, string $url, array $params = null): Response
    {
        $request = new Request( $url );


        switch( $method ){
            case 'GET' :
                if( $params && isset( $params['query'] ) ){
                    foreach( $params['query'] as $key => $value ){
                        $request->addGet( new Field( $key, $value ) );
                    }
                }
                break;
            case 'POST' :
                if( $params && isset( $params['form_params'] ) ){
                    foreach( $params['form_params'] as $key => $value ){
                        $request->addPost( new Field( $key, $value ) );
                    }
                }elseif($params && isset( $params['body'] )){
                    if(!isset($params['header']['Content-Type']) ){
                        $params['header']['Content-Type'] = 'application/json; charset=utf-8';
                    }
                    $request->setUserOpt( [CURLOPT_POSTFIELDS => $params['body']] );
                }
                break;
            default:
                throw new \InvalidArgumentException( "method eroor" );
                break;
        }

        if( isset( $params['header'] ) && !empty( $params['header'] ) && is_array( $params['header'] ) ){
            foreach( $params['header'] as $key => $value ){
                $string   = "{$key}:$value";
                $header[] = $string;
            }

            $request->setUserOpt( [CURLOPT_HTTPHEADER => $header] );
        }

        if( isset( $params['opt'] ) && !empty( $params['opt'] ) && is_array( $params['opt'] ) ){

            $request->setUserOpt($params['opt']);
        }
        return $request->exec();
    }

}
```

发起请求：

```php
<<?php
 /**
  * Created by PhpStorm.
  * User: root
  * Date: 18-10-12
  * Time: 上午11:07
  */
 
 namespace App\HttpController;
 
 
 use App\Utility\Curl;
 use EasySwoole\Http\AbstractInterface\REST;
 use EasySwoole\Spl\SplString;
 
 class User extends REST
 {
     
     function GETTest()
     {
         $request = new Curl();
         $params = [
             'query' => [
                 'nobase64' => 1,
                 'musicid' => '109332150',
                 'inCharset' => 'utf8',
                 'outCharset' => 'utf-8'
             ],
             'opt' => [
                 CURLOPT_REFERER => 'https://y.qq.com/n/yqq/song/001xiJdl0t4NgO.html'
             ]
         ];
         $content = $request->request('GET','https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric.fcg', $params);
         $string = new SplString($content);
         $content = $string->regex('/\{.*\}/');
         $json = json_decode($content, true);
         $lyric = $json['lyric'];
         $this->response()->write(html_entity_decode($lyric));
     }
 }
```