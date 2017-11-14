autolad.php  

    负责处理框架类的自动加载,在core.php的registerAutoLoader()被调用
    让我们分析来autolad的源码吧

    变量：
        protected $instance:此框架采用单例模式,这个变量负责储存自己类的实例，所以在框架运行会，此类每个进程有且只有一个对象
        protected $prefixes:这个变量存储 命名空间的与其对应的路径，我们可以通过调用函数来修改此数组的值来达到自己对框架的路径定制

    函数：
        static getInstance(): 此函数用于获得类的实例，如果类还没初始化，就会去初始化类，并把对象存储在$instance中
        __construct(): 类的构造函数，类初始化时会自动调用此函数
        protected register()：
                        此函数在类的构造函数中被调用，意味着，类只要初始化成功就会运行此函数，此函数里面执行了
                    spl_autoload_register(array($this, 'loadClass'));这是整个类的核心，就是这句话注册了自动加载机制，这句话的意思是，要是框架在运行中，发现了没有加载的类就自动执行此类中的loadclass函数;
        public addNamespace(): 
                        此函数在core的registerAutoloader中被调用了，用户也可以在框架开始运行时调用，此函数最少需要传入两个参数一个是命名空间，一个是路
                    径，比如传入（'ass','app/vonder/ass')就代表了定义命名空间为ass的类的真实路径是在app/vonder/ass里，如果传入的命名空间已经存在就会改原来的路径，用户一般在core的预处理函数调用此函数

        protected loadClass():
                        此函数由register注册，在框架运行中，发现未加载过的类就会自动调用此函数,传入的参数是未加载的类的命名空间，如app\ass\ssa,
                    取出斜杠最左边的值，和最右边值，$prefix=app 和 $relative_class  =ssa ， 将这两个参数传入loadMappedFile(),如果loadMappedFile（）加载成功则返回true
        protected loadMappedFile():
                        此函数被loadclass函数调用,通过prefixs参数的相应命名空间存储的路径，拼凑出真实路径，调用requireFile，传入真实路径，如果加载成功返回真，如果加载失败，或者命名空间不存在则返回假
        public requireFile(): 加载传入路径的类，如果类不U存在或者加载失败就返回假，否则返回真，此函数可以在event 的框架初始化事件被调用，主动加载一些类

        public importPath(): 同样拿来加载类的


    总结：
        autolad.php 使用了php的惰性加载机制,所有类不会在一开始就被加载，而是在用到的时候才被加载到，极大地提高了性能，用户也能在框架开始运行时候调用类的addnamespace方法来对命名空间的定制，和requirefile方法主动加载方法，

di.php

    此类ioc容器的功能(个人理解是全局超级无敌大数组)负责存储各个对象,用于保存一些不愿意销毁的变量和对象,值得注意的是进程信息独立，所以每个进程的容器对象其实不一样的让我们来分析它吧 

    变量：
        protected static $instance：此类使用单例模式,此变量用于保存此类的实例,以此来实现内存常驻
        protected $container：用于存储用户放入对象的变量

    函数：
        set():传入最少两个参数，第一个作为key，第二作为值放入$container[key][obj],之后多出的参数会被当成数组放入$container[key][params]
        delete():删除传入的key的对象
        clear()：清空$container
        get():通过传入key值找到相应的数组，判断该数组是不是一个对象key不要是一个对象直接返回，判断该数组是不是一个函数名，如果是一个函数的话则执行它，并返回执行结果，判断该数组的值是不是一个类名，如果是一个类名的则实例化该类，并且把这个key的值重新存为该对象并返回，如果key只是字符串则直接返回，如果key不存在则返回空

    总结：
        此类用于存储各种需要长时间存在的对象，如数据库连接对象，存入di，能够实现到数据库连接池的效果，进行长连接，一般work进程和task进程运行后，就不能对di对象进行删除，增加操作，因为每个进程的di对象是不同的，在一个进程对di操作，其他的进程的对象并没有变化，还有在框架初始化事件，最好不要往di数组里面添加对象，应该添加对象实现的方法，如果直接添加对象，swoole会在后台直接把这些对象复制到每个进程，每个进程是相互独立运行的，很容易照成多进程同时使用一个资源，发生非预期的错误




