使用validation验证器
---


> 仓库地址: [validation](https://github.com/illuminate/validation)

安装
------

```
composer require illuminate/validation
```

我们先单例validation验证器
```
namespace App\Vendor\Validators;


use Illuminate\Validation\Factory;

class Validator extends Factory
{
    public static function getInstance()
    {
        static $validator = null;
        if ($validator === null) {
            $test_translation_path = __DIR__.'/lang';
            $test_translation_locale = 'en';
            $translation_file_loader = new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, $test_translation_path);
            $translator = new \Illuminate\Translation\Translator($translation_file_loader, $test_translation_locale);
            $validator = new \Illuminate\Validation\Factory($translator);
        }
        return $validator;
    }

}
```
然后可以在控制器中使用
```
use App\Vendor\Validators\Validator;

// 在Index控制器类添加以下方法
function index(){
    //验证数据
    $data = [
        'title' => '123457',
        'status' => 1
    ];
    //验证规则
    $rules = [
        'title' => 'required|string|min:2|max:5',
        'status' =>'required|integer'
    ];
    //错误消息
    $messages = [
    ];
    //属性名称
    $attributes = [
        'title' => '名称',
        'status' => '状态',
    ];
    $validator = Validator::getInstance()->make($data, $rules, $messages, $attributes);
    if ($validator->fails()) {
        $this->response()->write($validator->messages()->first());
        //$this->response()->write($validator->errors());
        $this->response()->end();
    }
    $this->response()->write('ok');
}