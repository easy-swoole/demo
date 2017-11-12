request组件 :
request 有三个部分  
        mesasge 文件夹里面的 request 和serverrequest（serverrequest继承自request）http里面的 request

1.message里面的request：
		函数 ：
			__construct()：构造函数
	       	getRequestTarget()：获得请求的路径
			getMethod()：获得请求方法
			getUri()：获得请求的url
			withRequestTarget():设置请求路径
			withMethod():设置请求方法
			withUri():设置请求的url
	     	这里面的url是封装的类,利用pares_url()函数

2.serverrequest类继承自上面的request类
		变量：
			$attribute 用于存取一些额外的信息，可以在控制器中往这个变量里面放信息，注意变量不能多进程共享
			私有$cooikeParams 用于存放cookie数组，
			私有$parseBody 用于存放post数据
			私有$queryParams 用于存放get参数数组
			私有$serverParams 用于存放服务器参数数组
			私有$uploadFiles 用于存放上传文件的数组
		函数：
			构造函数和 一系列获得变量和设置变量的参数

3.http下的request类继承自上面的serverrequest类
此类用了单例模式所以每次请求只会初始化一次
		函数：
			__construct():在此函数传入了swoole扩展的request的对象，将request对象存入类变量
			依次执行
			initheaeder() 通过swoole的request对象的header遍历，调用父类的withAddHeader()函数增加继承下来的header字段
			initFiles()  通过swoole的request对象 的files属性 遍历，初始化自己的封装的file类 ，存到 file字段
			initCookie() 把swoole的request对象的cookie赋值出来
			initPost()	 把swoole的request对象的post赋值出来
			initGet()    把swoole的request对象的get赋值出来

		公有函数：
			getRequestParam()获得请求参数
			RequestParamValidate()验证参数合法性
			getSwooleRequest()获得request实例
			session()获得session实例
总结：
	框架运行的时候，会初始化一个http/request类，该类在一次请求时只会调用一次，初始化过程中会把swoole的request的参数全部提取出来复制到类中，便于统一操作；

response组建
response有两个类
		http的response类继承于message的response
1.message的response
		变量：
			$statusCode 状态码
			$responsePhare ’ok‘
			$cookies   存储cookie数组
		函数：
			getstatuscode() 获取状态码
			getresponsePhare()
			witgAddCookies()增加数组
			getCookies()获取数组
2.http的response

此类使用单例模式，用户每次请求只会实例化一次
在构造函数的时候，会将swoole的response的对象复制过来
   		函数：
			end() 将标志位设为1，意味着write结束了
			isendresponse() 判断是否结束
			write()向body写入数据，body的数据是拿来返回用的，body变量继承自						       message类		
			writejson()同理写入json数据
			redirect() 向header头写入跳转的url
			setcookie() 设置cookie
			forward()
			session()
			getswooleresponse()获得swoole原生的response
整体梳理：
在用户的请求事件，框架会调用swoole的response对象和request对象，
通过两个对象实例化出一个response类和request类
两个类分别封装类请求和返回各种方法
