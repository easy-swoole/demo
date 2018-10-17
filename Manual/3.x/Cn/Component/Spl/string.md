## 字符串

EasySwoole 提供了一些常用，方便开发者的基础方法，示例：

```php
$string = new \EasySwoole\Spl\SplString("Hello World");
```

设置字符串：

```php
function setString( string $string ) : SplString
```

切割字符串为指定长度的数组：

```php
function split( int $length = 1 ) : SplArray
```

```php
// 字符串为：Hello World
var_dump($string->split(2)->getArrayCopy());
/* 结果：
 array(6) {
    [0]=>
    string(2) "He"
    [1]=>
    string(2) "ll"
    [2]=>
    string(2) "o "
    [3]=>
    string(2) "Wo"
    [4]=>
    string(2) "rl"
    [5]=>
    string(1) "d"
 }

*/
```

间隔符拆分：

```php 
function explode( string $delimiter ) : SplArray
```

```php
// 字符串为：Easy,Swoole
var_dump($string->explode(',')->getArrayCopy()  );
/* 结果：
 array(2) {
    [0] =>
    string(4) "Easy"
    [1] =>
    string(6) "Swoole"
  }
*/
```

截取：

```php
function subString( int $start, int $length ) : SplString
```

> 返回 SplString 的可以用链式操作，下面不再介绍。
>
> 如：$string->subString(0,8)->split(2);

```php
// 字符串：EasySwoole
var_dump($string->subString(0,4)->__toString());
// 结果： Easy
$string->split(2);
/* 结果：
array(2) {
    [0] =>
    string(2) "Ea"
    [1] =>
    string(2) "sy"
  }
*/
// 也可以 $string->subString(0,4)->split(2);
```

编码转换：

```php
function encodingConvert( string $desEncoding, $detectList
	= [
		'UTF-8',
		'ASCII',
		'GBK',
		'GB2312',
		'LATIN1',
		'BIG5',
		"UCS-2",
	] ) : SplString
```

UTF8转换便捷方法：

```php
function utf8() : SplString
```

Unicode转成UTF8：

```php
function unicodeToUtf8() : SplString
```

转成Unicode：

```php
function toUnicode() : SplString
```

对比：

```php
function compare( string $str, int $ignoreCase = 0 ) : int
```

```php
// 字符串：EasySwoole
var_dump($string->compare('EasySwool'));
// 结果：1
```

移除字符串左边的字符： 

```php
function lTrim( string $charList = " \t\n\r\0\x0B" ) : SplString
```

移除字符串右边的字符：

```php
function rTrim( string $charList = " \t\n\r\0\x0B" ) : SplString
```

移除字符串两侧的字符：

```php
function trim( string $charList = " \t\n\r\0\x0B" ) : SplString
```

填充：

```php
function pad( int $length, string $padString = null, int $pad_type = STR_PAD_RIGHT ) : SplString
```

```php
// 字符串：EasySwoole
var_dump($string->pad(20,'.')->__toString());
// 结果：EasySwoole..........
```

重复：

```php
function repeat( int $times ) : SplString
```

```php
// 字符串 EasySwoole
var_dump($string->repeat(2)->__toString());
// 结果：EasySwooleEasySwoole
```

字符串长度：

```php
function length() : int
```

```php
// 字符串 EasySwoole
var_dump($string->length());
// 结果：10
```

全转成大写：

```php
function upper() : SplString
```

```php
// 字符串 EasySwoole
var_dump($string->upper()->__toString());
// 结果：EASYSWOOLE
```

全转成小写：

```php
function lower() : SplString
```

```php
// 字符串 EasySwoole
var_dump($string->lower()->__toString());
// 结果：easyswoole
```

剥去字符串中的 HTML 标签：

```php
function stripTags( string $allowable_tags = null ) : SplString
```

```php
// 字符串 <html>EasySwoole</html>
var_dump($string->stripTags()->__toString());
// 结果：EasySwoole
```

替换：

```php
function replace( string $find, string $replaceTo ) : SplString
```

```php
// 字符串 EasySwoole
var_dump($string->replace("Easy","Hello, Easy")->__toString());
// 结果：Hello, EasySwoole
```

两者之间的：

```php
function between( string $startStr, string $endStr ) : SplString
```

```php
// 字符串 EasySwoole
var_dump($string->between("Ea","le")->__toString());
// 结果：sySwoo
```

正则匹配：

```php
function regex( $regex, bool $rawReturn = false )
```

```php
// 字符串：http://www.easyswoole.com/index.html
var_dump($string->regex("@^(?:http://)?([^/]+)@i"));
// 结果：http://www.easyswoole.com
```

是否存在：

```php
function exist( string $find, bool $ignoreCase = true ) : bool
```

```php
// 字符串 http://www.easyswoole.com/index.html
var_dump($string->exist("easyswoole"));
// 结果：true
```

可以撸的烤串：

```php
function kebab() : SplString
```

```php
// 字符串 KaoChuanKaoChuanKaoChuan
var_dump($string->kebab()->__toString());
// 结果：kao-chuan-kao-chuan-kao-chuan
```

扭一扭：

```php
function snake( string $delimiter = '_' ) : SplString
```

```php
// 字符串 PiGuNiuYiNiu
var_dump($string->snake('_')->__toString());
// 结果：pi_gu_niu_yi_niu
```

起起伏伏：

```php
function studly() : SplString
```

```php
// 字符串 User_info-Profile-goods_message
var_dump($string->studly()->__toString());
// 结果：UserInfoProfileGoodsMessage
```

驼峰：

```php
function camel() : SplString
```

```php
// 字符串 User_info_Profile_goods_message
var_dump($string->camel()->__toString());
// 结果：userInfoProfileGoodsMessage
```

用数组逐个字符：

```php
function replaceArray( string $search, array $replace ) : SplString
```

```php
// 字符串 你好啊，你在吗
var_dump($string->replaceArray('你',['我','他'])->__toString());
// 结果：我好啊，他在吗
```

替换字符串中给定值的第一次出现：

```php
function replaceFirst( string $search, string $replace ) : SplString
```

```php
// 字符串 你好啊，你在吗
var_dump($string->replaceFirst('你','我')->__toString());
// 结果：我好啊，你在吗
```

替换字符串中给定值的最后一次出现：

```php
function replaceLast( string $search, string $replace ) : SplString
```

```php
// 字符串 你好啊，你在吗，你在吗
var_dump($string->replaceLast('你','他')->__toString());
// 结果：你好啊，你在吗，他在吗
```

以一个给定值的单一实例开始一个字符串：

```php
function start( string $prefix ) : SplString
```

```php
// 字符串 user_table
var_dump($string->start('easyswoole_')->__toString());
// 结果：easyswoole_user_table
```

在给定的值之后返回字符串的其余部分：

```php
function after( string $search ) : SplString
```

```php
// 字符串 easyswoole.user.png	
var_dump($string->after('.')->__toString());
// 结果：user.png
```

在给定的值之前获取字符串的一部分：

```php
function before( string $search ) : SplString
```

```php
// 字符串 easyswoole.jpg
var_dump($string->before('.')->__toString());
// 结果：easyswoole
```

确定给定的字符串是否以给定的子字符串结束：

```php
function endsWith( $needles ) : bool
```

```php
// 字符串 easyswoole.jpg
var_dump($string->endsWith(['png','gif','jpg']));
// 结果：true
```

确定给定的字符串是否从给定的子字符串开始

```php
function startsWith( $needles ) : bool
```

```php
// 字符串 easyswoole.jpg
var_dump($string->startsWith(['e','easyswoole','es']));
// 结果：true
```