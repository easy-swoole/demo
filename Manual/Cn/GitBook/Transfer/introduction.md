# 前言

------

从一个框架切换到另一个框架是一个比较痛苦的过程，需要学习一套新的框架如何使用，同时原有的代码还需要大量修改，是一件令人非常苦恼的事情，特别是在对于新的框架不熟悉的情况下，往往不知道从何入手

本章节的文档正是为了解决这个烦恼，介绍了从目前主流的两个PHP框架`ThinkPHP`和`Laravel`进行迁移，保持代码基本无痛切换，降低迁移项目的成本，同时更快的享受到`easySwoole`提供的常驻内存，方便的异步任务等传统框架难以实现的特性

开始之前
------

在开始迁移之前，建议阅读一遍`easySwoole`的文档，对框架有一个大致的了解，方便后续进行迁移，本章节的内容将使用`Composer`作为主要的包管理工具，请务必阅读`项目初始化`以及`Composer集成`章节来帮助您更快的部署框架以及完成必要的基础准备工作

本章节将会介绍如何将一些主要组件集成到`easySwoole`，计划介绍的组件以及文档进度请阅读参考下方表格，如有不完善的地方或者需要用到但是没有文档的组件，请加入交流群`633921431 `一起交流或完善文档

|组件|所属框架|仓库地址|文档进度|
|:---:|:---:|:---:|:---:|
|数据库|`ThinkPHP`|[ORM](https://github.com/top-think/think-orm)|已完成|
|模板类|`ThinkPHP`|[Template](https://github.com/top-think/think-template)|已完成|
|验证器|`ThinkPHP`|[Validate](https://github.com/top-think/think-validate)|已完成|
|缓存类|`ThinkPHP`|[Cache](https://github.com/top-think/think-cache)|已完成|

|组件|所属框架|仓库地址|文档进度|
|:---:|:---:|:---:|:---:|
|数据库|`Laravel`|[Database](https://github.com/illuminate/database)|完善中|
|模板类|`Laravel`|[Blade](https://github.com/jenssegers/blade)|完善中|
|分页类|`Laravel`|[Pagination](https://github.com/illuminate/pagination)|完善中|
|验证器|`Laravel`|[Validation](https://github.com/illuminate/validation)|完善中|
|缓存类|`Laravel`|[Cache](https://github.com/illuminate/cache)|完善中|