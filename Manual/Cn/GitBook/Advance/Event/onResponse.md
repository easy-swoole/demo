# 响应事件
在任何的控制器响应结束后，均会执行该事件,该事件预留于做分析记录。
例如结合onRequest事件做慢日志记录。
```
function onRequest(Request $request, Response $response)
{
    // TODO: Implement onRequest() method.
    $request->withAttribute("startTime",microtime(true));
}
function onResponse(Request $request,Response $response)
{
    // TODO: Implement afterResponse() method.
    $end = microtime(true);
    $ret = $end - $request->getAttribute("startTime");
    Logger::getInstance()->console("your request take {$ret}");
}
```