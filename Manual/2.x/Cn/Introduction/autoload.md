## 自动加载

可以在composer.json内进行配置。如：

```Json
"autoload": {
    "psr-4": {
        "App\\" : "App/",
        "EasySwoole\\" : "Conf/",
        "YourTest\\":"tests/"
    },
    "files":["lib/ClassTest.php"]
}
```

更详情文档请见，composer官方文档。https://getcomposer.org/doc/