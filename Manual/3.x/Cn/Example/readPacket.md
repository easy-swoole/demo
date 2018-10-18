# 场景

# 数据库结构
## 红包列表
redPacketId,account,money,allNum(红包个数),userNum（使用个数）,leftMoney

## 红包领取列表
account,redPacketId,money,groupId(在哪个群领取的)，hash(uq,md5(account,redPacketId,groupId))

# 用户发包

用户发的红包，直接插入红包列表

# 抢包

任意一个用户，在某个群发起抢红包需求，那么生产数据结构
```
{
    account:xxxx,
    groupId:xxx,
    redPacketId:xxx,
    hash:md5(account,redPacketId,groupId),
    time:unixTime
}
```
插入队列，等待消费，而前端（客户端）用hash轮训结果，假定超时1s。

服务端，消费队列，每次消费到一个数据包，那么优先判定数据包生产时间有没有超过1s，为避免出现临街问题，可将超时时间设为0.9s，
假定没有超时，那么继续去判断用户领取红包列表里面有没有领取记录，如果没有，就执行领取并插入记录。
而前端也可以查询到该hash，知道成功领取了红包，否则超时后就失败。

# 如何保证每个人都能领到钱而不会超支红包金额

最简单的，如果有1000元的红包发了100个包，那么每个人可以领取的金额就是
0.01元+random(0,left),这样就保证了每个人可以领取到。注意，left初始金额是1000-100*0.01


> 以上仅为大致思路