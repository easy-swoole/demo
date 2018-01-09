验证码组件
------

用于生产验证码，支持自定义验证码字体，使用Composer安装

```
composer require easyswoole/verifycode
```

配置定义
------

组件本身提供了默认配置，即使不做任何设置也可以直接生成验证码，需要对验证码进行自定义配置可以使用组件提供的`Conf`类进行动态配置

```
use easySwoole\VerifyCode\Conf;
$Conf = new Conf();
```

#### 设置字符集合
可以自定义验证码生成使用的字符集合设置后从集合中随机选取，不设置则从`[0-9A-Za-z]`中随机选取

```
$Conf->setCharset('123456ABCD');
```

#### 设置背景色
设置验证码的背景颜色，不设置默认使用白色，支持使用完整HEX，缩写HEX和RGB值设置

```
$Conf->setBackColor('#3A5FCD');
$Conf->setBackColor('CCC');
$Conf->setBackColor([30, 144, 255]);
```

#### 设置文字颜色
设置验证码的背景颜色，不设置则随机生成一个颜色，支持使用完整HEX，缩写HEX和RGB值设置

```
$Conf->setFontColor('#3A5FCD');
$Conf->setFontColor('CCC');
$Conf->setFontColor([30, 144, 255]);
```

#### 设置混淆
支持两种混淆方式，默认两种混淆都是关闭的，需要手动开启

```
// 开启或关闭混淆曲线
$Conf->setUseCurve();
// 开启或关闭混淆噪点
$Conf->setUseNoise();
```

#### 设置字体
默认验证码类已经带有6种字体，如果需要增加自己的字体库来提高识别难度，或者指定使用的字体，可以如下设置，注意字体路径需要使用绝对路径，即文件的完整路径

```
// 增加单个字体传入路径字符串
$Conf->setFonts('path/to/file.ttf');
// 增加多个字体传入路径的数组
$Conf->setFonts(['path/to/file1.ttf', 'path/to/file2.ttf']);
```

```
// 指定生成使用的字体文件
$Conf->setUseFont('path/to/file.ttf');
```

#### 其他设置
可以指定图片宽高，字体大小，随机生成的验证码位数等

```
// 设置图片的宽度
$Conf->setImageWidth(400);
// 设置图片的高度
$Conf->setImageHeight(200);
// 设置生成字体大小
$Conf->setFontSize(30);
// 设置生成验证码位数
$Conf->setLength(4);
```

#### 链式调用
为了更流畅的进行设置，所有的配置项均支持链式调用

```
$Conf = new Conf();
$Conf->setUseNoise()->setUseCurve()->setFontSize(30);
```

------

可以使用上方的动态配置，将设置好的配置类传入给验证码类，
```
$Conf = new Conf();
$Conf->setFontSize(30);
$VCode = new VerifyCode($Conf);
```

如果配置比较多，也需要全站统一验证码配置，可以将验证码的配置放入配置文件，在生成时读取配置，验证码的设置类继承自`SplBean`，可以在设置好后使用配置类的`toArray`方法直接获得配置数组，实例化验证码时，读取数组重新生成Conf类即可

生成
------

初始化配置后即可生成验证码，可以随机生成，也可以指定需要生成的验证码

```
$VCode = new VerifyCode($Conf);

// 随机生成验证码
$Code = $VCode->DrawCode();

// 生成指定验证码
$Code = $VCode->DrawCode('MyCode');
```

生成好验证码后结果是一个`Result`类，可以使用`getImageBody`获取验证码的图片内容，使用`getImageStr`获得验证码字符，`getMineType`获得验证码的Mine信息