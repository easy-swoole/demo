# Mysql索引降维
很多人都知道，mysql有索引这个概念，但是却很少去较真，如何利用索引去对数据降维，以提高查询速度。

举个常见的场景，那就是用户日志（订单），例如，在中国移动的通话记录系统中，需要记录
呼出手机号，被呼号码和呼出时间，而在该系统中，最常见或用的最多的需求，就是查询某个用户在某个时间段内的通话记录。我们做出以下数据特征模拟：

- 一个月内，有一万个账户，每天打出三万通话记录。

数据模拟生成代码：
```php
<?php

require 'vendor/autoload.php';

\EasySwoole\EasySwoole\Core::getInstance()->initialize();

function generatePhoneList()
{
    $list = [];
    for ($i=0;$i <= 10000; $i++){
        array_push($list,'155'.\EasySwoole\Utility\Random::number(8));
    }
    return $list;
}

function generateTimeList(int $startTime,$max = 30000)
{
    $list = [];
    for ($i=0;$i<=$max;$i++){
        //模拟从早上7点到凌晨
        $t = mt_rand(
            25200,86400
        );
        array_push($list,$startTime+$t);
    }
    sort($list);
    return $list;
}

$config = \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL');
$db = new \App\Utility\Pools\MysqlPoolObject($config);
$phoneList = generatePhoneList();
//模拟一个月的时间数据
$start = strtotime('20180101');
//
for ($i = 0; $i<=30; $i++){
    $timeList = generateTimeList($start);
    foreach ($timeList as $time){
        $phone = $phoneList[mt_rand(0,10000)];
        $target = $phoneList[mt_rand(0,10000)];
        $db->insert('user_phone_record',[
            'phone'=>$phone,
            'targetPhone'=>$target,
            'callTime'=>$time
        ]);

    }
    $start += 86400;
}
```

> 在本次讲解中，以数据量50万为例子，懒得等数据生成。phone，callTime为索引字段。


## 需求
查询某个时间段内某个账户的全部通话记录。
那么此刻，很多人可能就直接写：
```
SELECT * FROM test.user_phone_record where callTime >=  1514768050 and  callTime <= 1514871213 and  phone = 15587575857;
```
以上语句在我的测试机中执行了0.26s，但是，若我调整一下where 的顺序：
```
SELECT * FROM test.user_phone_record where phone = 15587575857 and callTime >=  1514768050 and  callTime <= 1514871213 ;
```
那么久仅仅需要0.1s，节约了一半的时间。那么这两个看起来差不多的语句，为啥执行的时间不一样呢。

## 直观解释

首先，我们分别执行两个sql并查看结果(别说为啥不用explain和profiling解释，只想给你们最直观的解释)。
```
 SELECT count(*) FROM test.user_phone_record where phone = 15587575857 
```
> 结果为15条记录。
```
SELECT count(*) FROM test.user_phone_record where callTime >=  1514768050 and  callTime <= 1514871213 
```
> 结果为76491条记录。

那么最直观的解释来了：先where callTime再where phone，那么mysql做的事情就是：
先找出76491条记录，再从76491条记录中找出account为15587575857的记录。同理，先where phone，再筛选时间，肯定是更加快的了。


## 为什么会这样？
这是和特定的数据结构与场景才可以这样去调优的，由前提条件：

- 一个月内，有一万个账户，每天打出三万通话记录

可知，单用户的通话频度不高，因此，先定位phone索引集再排除时间的搜索方式，肯定比先定时间再定账户的效率高。

> 注意，这是特定场景！！！具体请以explain与profiling去分析，MYSQL的执行解释器，没有这么简单。

