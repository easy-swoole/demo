#用户表
## admin_list  超级管理员列表  
   - adminId  ```INT NOT_NULL QU AI``` 
   - adminName ```VARCHAR(15) NOT_NULL DEFAULT='超级管理员'```
   - adminAccount  ```VARCHAR(18) QU INDEX```
   - adminPassword ```VARCHAR(32)```
   - adminSession ```varchar```
   - adminLastLoginTime ```INT(10)```
   - adminLastLoginIp ```VARCHAR(20)``` 

## user_list 普通用户表
   - userId 
   - userName ```varchar 32 会员昵称```
   - userAccount ```varchar 18会员账号```
   - userPassword ```varchar 32 会员密码```
   - phone ```varchar 18 会员手机号码```
   - money ```int 余额```
   - addTime ```int ```
   - lastLoginIp ```varchar 32 最后登录ip```
   - lastLoginTime ```int 最后登录时间```
   - userSession ```varchar```
   - state ```tinyint 0禁用,1正常```

# 系统设置表
## banner_list 
   - bannerId
   - bannerName ```varchar 32 轮播图名称```
   - bannerImg ```varchar```
   - bannerUrl ```varchar 跳转地址```
   - bannerDescription ```varchar 255 介绍```
   - state ```tinyint 状态0隐藏 1正常```   