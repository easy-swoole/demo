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
