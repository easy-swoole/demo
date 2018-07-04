# 数据校验
EasySwoole提供了基础的数据校验。
```
$data = array(
    "a"=>1,
    "b"=>array(
        "age"=>2,
        "b2"=>null
    ),
    "c"=>array(
        "age"=>3,
        "b2"=>"asas"
    ),
    "URL"=>'http://www.baidu.com',
    "regex"=>"121sssss212"
);
$validate = new \Core\Utility\Validate\Validate();
$validate->addField("a")->withRule(\Core\Utility\Validate\Rule::REQUIRED)
    ->withMsg("a字段不能为空")->withRule(\Core\Utility\Validate\Rule::BETWEEN,-1,3)
    ->withMsg("区间范围错误");
$validate->addField("b.b2")->withRule(\Core\Utility\Validate\Rule::REQUIRED)
    ->withMsg('b字段不能为空')->withMsg('b2的全局错误信息')
    ->withRule(\Core\Utility\Validate\Rule::ALPHA);
$validate->addField("*.age")->withRule(\Core\Utility\Validate\Rule::NUMERIC)
    ->withRule(\Core\Utility\Validate\Rule::BETWEEN,1,2);
$validate->addField("URL")->withRule(\Core\Utility\Validate\Rule::ACTIVE_URL);
$validate->addField("regex")
    ->withRule(\Core\Utility\Validate\Rule::REGEX,"/^\d*$/")->withMsg('不符合正则');
$result = $validate->validate($data);
var_dump($result->hasError());
var_dump($result->all());
var_dump($result->getError("b.b2")->first());
var_dump($result->getError("not exist")->first());
```

## 支持规则列表：
```
/*
\Core\Utility\Validate\Rule
*/
class Rule
{
    const ACTIVE_URL = 'ACTIVE_URL';
    const ALPHA = 'ALPHA';
    const BETWEEN = 'BETWEEN';
    const BOOLEAN = 'BOOLEAN';
    const DATE = 'DATE';
    const DATE_AFTER = 'DATE_AFTER';
    const DATE_BEFORE = 'DATE_BEFORE';
    const DIFFERENT = 'DIFFERENT';
    const FLOAT = 'FLOAT';
    const IN = 'IN';
    const INTEGER = 'INTEGER';
    const IP = 'IP';
    const ARRAY_ = 'ARRAY_';
    const LEN = 'LEN';
    const NOT_IN = 'NOT_IN';
    const NUMERIC = 'NUMERIC';
    const MAX = 'MAX';
    const MAX_LEN = 'MAX_LEN';
    const MIN = 'MIN';
    const MIN_LEN = 'MIN_LEN';
    const OPTIONAL = 'OPTIONAL';
    const REGEX = 'REGEX';
    const REQUIRED = 'REQUIRED';
    const SAME = 'SAME';
    const TIMESTAMP = 'TIMESTAMP';
    const URL = 'URL';
}
```
> 具体规则的验证原理，在\Core\Utility\Validate\Func中。
