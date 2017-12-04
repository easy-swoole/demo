core.php 和 server.php 源码分析

首先我扪要一些swoole的基础

      swoole 的整个进程种类是 manager进程，master进程，work进程，task进程
      maskter进程：Swoole的主进程，是一个多线程的程序。其中有一组很重要的线程，称之为Reactor线程。它就是真正处理TCP连接，收发数据的线程。把接受到的数据分配给worker进程
      mananger进程：负责管理work进程和task进程，如果有进程死了，就会重新开一个进程
      worker进程：正常处理业务逻辑的进程
      task进程：负责处理异步任务的进程，任务由work进程投递过来
开启一个多进程swoole http服务器需要注册的函数：

      onstart事件
           在此事件之前Swoole Server已进行了如下操作
           已创建了manager进程
           已创建了worker子进程
           已监听所有TCP\/UDP端口
           已监听了定时器
      onshutdown事件
                 在此之前Swoole Server已进行了如下操作
                 已关闭所有线程
                 已关闭所有worker进程
                 已close所有TCP\/UDP监听端口
                 已关闭主Rector
      onworkerstart事件
                 task进程和work进程开启时都会调用此函数
      onworkerstop事件
                 task进程和work进程结束都会调用此函数
      onrequest事件
                用户发送请求会触发此函数，此函数会随机在一个work进程种
      ontask事件
                当系统投递task任务时会触发此函数（注意投递一次会就占用一个task进程，直到任务结束，task进程才会空闲，要是短时间投递任务数超过task进程数，任务就会进入队列排队）
      onfinish事件
                当task任务结束时会触发此任务，注意想要开启task功能必须注册这两个事件
      onworkerror事件
                当work进程出错时会触发此函数

下面我们来分析server.php
 server.php
 
      函数：
                getInstance\(\):获得sever服务对象的实例，此类采用单例模式，当框架运行的时候，全局每个进程有且只有一个server对象，每个对象是相互独立的
                \_\_construct\(\):构造函数，这个函数只负责对server基本的配置
                is\_start\(\):判断服务是否已经启动
                stratServer（）：启动服务，在这个函数会调用很多其他函数，对server对象各个事件进行注册，
                getServer（）：获得swoole的server对象
                私有workStartEvent\(\):在startserver（）被调用，实现了的功能是注册swoole服务器的onworksart事件，注册事件调用的是event里的onworkerstart函数，在这个事件里面一般是启动定时器等等，这个事件会在每个worker进程和task进程开启的时候被触发
                私有workStopEvent\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的onwrokstop事件，注册事件里面调用了event的onworkstop函数
                私有onTaskEvent\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的ontask事件，注册事件里面调用了event的ontask函数，
                私有onFinish\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的ontask事件，注册事件里面调用了event的ontask函数，
                私有beforeWorkStart\(\):在startServer（）被调用，此函数的功能很大，在swoole启动前对server做了很多的定制，如可以开一个独立的进程，ioc注入，websocket事件回调
                私有serverStartEvent\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的onstart事件
                私有serverShutdownEvent\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的onshutdown事件
                私有workErrorEvent\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的onworkerror事件
                私有pipeMessage\(\):在startServer（）被调用，实现的功能是注册了swoole服务器的onpipeMessage事件
                私有listenRequest\(\):
                     这是整个框架最核心的部分，这个函数注册了server的监听事件，我们来一句一句分析：
                        1 首先把swoole自带的request对象和response对象传入事先封装好的request类和response类 实例化出一个 request2对象和 response2对象 ，
                        2 调用event用户事先定义好的onrequest事件，相当于对所有请求进行了一次拦截，这这个事件中可以防止恶意工具
                        3 调用Dispatcher 事件 ，这个事件里面 先对request对象进行了路由，然后查找到对应的控制器，执行对应控制器的内容，控制器里面会使用wirte等方法将数据写入事先封装好的response对象
                        4 调用event的response 事件,\(用户可以在此事件进行自己的处理，如过滤返回的数据\)
                        5 框架开始拼凑要返回客户端的信息 ，状态码 header头， cookies ，还有response对象里面的body信息（控制器里面write出来的数据）
                        6 框架返回数据 response-&gt;end

下面我们来分析core.php 框架的启动类 采用单例模式，当框架运行成功后，每个进程只会只有一个对象
 core.php
 
      函数：
                run（）：这里会调用server类启动框架
                frameWorkInitIalize\(\):这里会进行框架的基本的初始化，判断php版本，注册自动加载，定义全局变量，建立文件夹，注册错误，调用event的frameInitialize和frameInitialized事件
                defineSysConst\(\):定义系统路径
                sysDirectoryInit\(\):创建框架运行时需要的目录，如日志目录等
                registerAutoloader\(\):注册自动加载函数，并且设置一些命名空间的路径
                reqisterErrorHandler\(\):注册错误函数
                preHandle\(\):
                           此函数能调用一个匿名函数，匿名函数存在在core类中的perCall里面,这个变量会在类初始化时传入，可以在server文件初始化core时传入一个匿名函数，对框架进行自己的定制，极大的方便了开发者

总结：easyswoole 为我们封装了swoole,默认运行es框架，就已经运行了一个多进程swoole服务器，我们在swoole每个重要的回调函数里面，都会调用even.php里面事件，所以只需要在even事件里面写我们的事件就好了极大的方便了开发者
