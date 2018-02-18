## Curl

#### 命名空间地址

EasySwoole\Core\Utility\Curl\Request

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

- EasySwoole\Core\Utility\Curl\Cookie `cookie` 

```php
public function addCookie(Cookie $cookie):EasySwoole\Core\Utility\Curl\Request
```

添加POST参数：

- EasySwoole\Core\Utility\Curl\Field `field`
- bool `isFile ` 是否为文件

```php
public function addPost(Field $field,$isFile = false):EasySwoole\Core\Utility\Curl\Request
```

添加GET参数：

- EasySwoole\Core\Utility\Curl\Field `field`

```php
public function addGet(Field $field):EasySwoole\Core\Utility\Curl\Request
```

添加用户信息：

- array `opt` 用户信息
- bool `isMerge` 是否合并

> 如：opt 可以设置头部信息

```php
public function setUserOpt(array $opt,$isMerge = true):EasySwoole\Core\Utility\Curl\Request
```

执行请求：

```php
public function exec():EasySwoole\Core\Utility\Curl\Response
```

获得用户信息：

```php
public function getOpt():array
```



## 自定义封装示例

为了方便自己习惯再封装一下自己喜欢的套路，以下仅为示例代码：

```php
<?php

namespace yourapp;

use EasySwoole\Core\Utility\Curl\Response;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Field;

class Curl
{
	public function __construct()
	{

	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array  $params
	 */
	public function request( string $method, string $url, array $params = null ) : Response
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
		return $request->exec();
	}
}
```

发起请求：

```Php
<?php 

namespace yourapp;

class Test{
    public function testRequest(){
        $response =  $this->request( "POST", "http://www.easyswoole.com", [
            'header' => ['Access-Token' = "xxxxxxxxxxxxxxxxxxxxxxx"],
            'query' => ['keywords'=> '大吉大利 今晚吃鸡'],
            'form_params' => ['title'=>'新添加一条记录','body'=>'文章内容']
        ] );
    }
}
```