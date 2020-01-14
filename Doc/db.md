
## user_list 普通用户表
   - userId
   - userAccount ```varchar 18 会员账号 ```
   - userPassword ```varchar 32 会员密码 ```
   - userKey ```varchar 32 用户登录标识 ```
   
## application_list 
   - appId
   - appName ```varchar 32 应用名称```

## user_application_login_list
   - id 
   - appId  ```int 11  ```
   - userId ```int 11 ```
   - appSecret ```varchar 32 appid的授权key```
   - expireTime ```int 11 失效时间```