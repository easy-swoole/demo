### Worker会不会同时被两客户端访问？
不会。
### 单例模式写法导致数据一直存在是为什么？
因为easyswoole是常驻内存的，static使用的时候要注意时机释放，详细文档请见：[《swoole_server中内存管理机制》](https://wiki.swoole.com/wiki/page/324.html)
### 用了很多第三方类库都存在有`$_GET`，`$_POST`等超全局变量，而swoole默认情况下值是空的怎么办？
可以在Event.php里的OnRequest方法里对超全局变量进行赋值。
```
$_GET    = isset( $request->getSwooleRequest()->get ) ? $request->getSwooleRequest()->get : [];
$_POST   = isset( $request->getSwooleRequest()->post ) ? $request->getSwooleRequest()->post : [];
$_COOKIE = isset( $request->getSwooleRequest()->cookie ) ? $request->getSwooleRequest()->cookie : [];
$_FILES  = isset( $request->getSwooleRequest()->files ) ? $request->getSwooleRequest()->files : [];
$server  = $request->getSwooleRequest()->server;
$_SERVER = [];
if( isset( $server ) ){
	foreach( $server as $key => $value ){
		$_SERVER[strtoupper( $key )] = $value;
	}
}
var_dump( $_SERVER );

```


