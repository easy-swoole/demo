# Warning
## Remember
- Do not use sleep anywhere,sleep can make the process block . exit/die is danger for swoole , it will cause the worker exit.
- You can catch the fatal error by register_shutdown_function when a worker is exit due to fatal error.
- set_exception_handler function is not support in swoole ,and you must try/catch all the Exception or the worker will exit . 
- Use require_once or include_once to replace require and include ,in case redeclare a function or class.
- All of the data after server start will be independent between different process  (just like pcntl_fork()) .
- All of the class and folder must be Big-hump.