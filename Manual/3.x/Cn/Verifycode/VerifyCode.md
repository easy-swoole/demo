## 验证码生成
### VerifyCode.php
VerifyCode验证码操作类,如果不传入Config实例,则自动实例化一个  

#### 调用方法:
```
$config = new Conf();
$code = new \EasySwoole\VerifyCode\VerifyCode($config);
$code->DrawCode();//生成验证码,返回一个Result对象
``` 

### Result.php 
验证码结果类,由VerifyCode验证码操作类调用 DrawCode() 方法时创建并返回  

方法列表:
```php
 /**
     * 获取验证码图片
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    function getImageByte()
    {
        return $this->CaptchaByte;
    }

    /**
     * 返回图片Base64字符串
     * @author : evalor <master@evalor.cn>
     * @return string
     */
    function getImageBase64()
    {
        $base64Data = base64_encode($this->CaptchaByte);
        $Mime = $this->CaptchaMime;
        return "data:{$Mime};base64,{$base64Data}";
    }

    /**
     * 获取验证码内容
     * @author : evalor <master@evalor.cn>
     * @return mixed
     */
    function getImageCode()
    {
        return $this->CaptchaCode;
    }

    /**
     * 获取Mime信息
     * @author : evalor <master@evalor.cn>
     */
    function getImageMime()
    {
        return $this->CaptchaMime;
    }

    /**
     * 获取验证码文件路径
     * @author: eValor < master@evalor.cn >
     */
    function getImageFile()
    {
        return $this->CaptchaFile;
    }
```
