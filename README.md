# EASYSWOOLE DEMO
## 关于具体demo在哪
demo/3.x分支对应了EasySwoole3.x版本的demo,3.x主要是easyswoole基础使用的例子，其他使用请看3.x对应的分支。

## 如何运行DEMO

安装项目时请不要覆盖默认的配置文件以及EasySwooleEvent事件注册文件

### 安装easyswoole
```bash
git clone https://github.com/easy-swoole/demo.git demo
cd demo && composer install
php vendor/bin/easyswoole install
```
### 配置数据库
在dev.php中的MYSQL配置项中配置数据库
### 安装项目数据库
运行/Doc/sql.sql文件
### 启动项目
````
php easyswoole start
````
### auth 2.0执行步骤
#### 登陆接口,获取会员token
访问:`/Api/User/Auth/login?userAccount=toncico&userPassword=123456`  

输出:  

`{"code":200,"result":{"userAccount":"tioncico","userId":1,"userKey":"44ed7e8e4a4c2ce609c08166dfcdb081","userSession":"44ed7e8e4a4c2ce609c08166dfcdb081"},"msg":null}`

#### 带上userSession通过application接口获取appSecret
访问:`/Api/User/Application/getSecret?userSession=44ed7e8e4a4c2ce609c08166dfcdb081&appId=1`  

输出:  

`{"code":200,"result":"a2c1ed89dfc428838f0124bd4381d52f","msg":"获取appSecret成功"}`

#### 通过`appSecret`可反查会员信息(无需userSession)
访问:`/Api/Common/Application/getUserInfo?appSecret=a2c1ed89dfc428838f0124bd4381d52f&appId=1` 
输出:  

`{"code":200,"result":{"userId":1,"userAccount":"tioncico"},"msg":"获取用户信息成功"}`

## 请先认真阅读手册 再进行体验

- [EASYSWOOLE在线手册](https://www.easyswoole.com)
- QQ交流群
    - VIP群 579434607 （本群需要付费599元）
    - EasySwoole官方一群 633921431(已满)
    - EasySwoole官方二群 709134628
    
- 商业支持：
    - QQ 291323003
    - EMAIL admin@fosuss.com    