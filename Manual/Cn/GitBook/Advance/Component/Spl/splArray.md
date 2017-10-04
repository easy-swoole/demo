# SplArray
SplArray其实是对ArrayObject的实现。
```
$spl = new \Core\Component\Spl\SplArray(array(
    "a"=>1,
    "b"=>array(
        "sub"=>'b1',
        "sub2"=>"b2"
    ),
    "c"=>array(
        "sub"=>'c1',
        "sub2"=>"c2"
    )
));
```
## get
```
var_dump($spl->get("a"));
int(1)

var_dump($spl->get("c.sub"));
string(2) "c1"

var_dump($spl->get("*.sub"));
array(2) {
    ["b"]=>
    string(2) "b1"
    ["c"]=>
    string(2) "c1"
}
```
## set
```
$spl->set("a",2);
var_dump($spl->get("a"));
$spl->set("c.sub",2);
var_dump($spl->get("c.sub"));
```
## getArrayCopy
```
var_dump($spl->getArrayCopy());
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  array(2) {
    ["sub"]=>
    string(2) "b1"
    ["sub2"]=>
    string(2) "b2"
  }
  ["c"]=>
  array(2) {
    ["sub"]=>
    string(2) "c1"
    ["sub2"]=>
    string(2) "c2"
  }
}
```
## __toString
```
echo $spl;
{"a":1,"b":{"sub":"b1","sub2":"b2"},"c":{"sub":"c1","sub2":"c2"}}
```
> 注意：SplArray中对toString方法的实习实际上是json_encode()