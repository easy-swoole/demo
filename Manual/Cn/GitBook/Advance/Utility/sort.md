# 排序
## 快速排序
```
var_dump(\Core\Utility\Sort::quickSort(array(
   5,3,6,1,9,10,22,7
)));
```
## 冒泡排序
```
var_dump(\Core\Utility\Sort::bubbleSort(array(
   5,3,6,1,9,10,22,7
)));
```
## 多维数组排序
```
var_dump(\Core\Utility\Sort::multiArraySort(array(
    array(
        "name"=>'张三',
        "age"=>33
    ),
    array(
        "name"=>'李四',
        "age"=>21
    ),
    array(
        "name"=>'王五',
        "age"=>29
    )
),"age"));
```

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>