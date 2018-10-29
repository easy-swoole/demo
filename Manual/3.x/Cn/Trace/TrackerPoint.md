## TrackerPoint 
该类为调用点,记录了一次调用点的全部数据

### 属性列表
```php
<?php
const STATUS_SUCCESS = 1;//调用状态 成功
const STATUS_FAIL = 0;  //调用状态 失败
const STATUS_NOT_END = -1;//调用状态 未结束

private $pointName;      //调用点名称
private $pointStartTime; //开始时间
private $pointEndTime;  //结束时间
private $pointStatus = self::STATUS_NOT_END;//当前状态
private $pointEndArgs = []; //结束参数
private $pointStartArgs = [];//开始调用时的参数
private $pointCategory; //调用类型
private $pointFile;     //调用文件
private $pointLine;    //调用行数

private $hasEnd = 0;  //是否已经结束
private $pointTakeTime = -1;  //耗时
```
### 方法列表
#### __construct  
初始化调用点数据

#### endPoint(string $name,$args,$category) 
结束调用 
#### getPointName()  
获取调用点名称
#### getPointStartTime()  
获取开始时间
#### getPointEndTime()  
获取结束时间
#### getPointStatus()  
获取当前状态
#### getPointEndArgs()  
获取结束参数
#### getPointStartArgs()  
获取开始调用时的参数
#### getPointCategory()  
获取调用类型
#### getPointFile()  
获取调用文件
#### getPointLine()  
获取文件调用函数
#### __toString()  
将类属性转为字符串,记录到日志或输出
