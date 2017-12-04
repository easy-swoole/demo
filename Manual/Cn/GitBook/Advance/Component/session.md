Session
------

以下是一个封装好的Session操作类，可以简化对Session的操作，同时也展示了如何用框架本身的方法操作Session

```
<?php

class SessionFacade
{
    /**
     * Set Session
     * @param $name
     * @param $value
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    static function set($name, $value = null)
    {
        $SessionInstance = Response::getInstance()->session();
        if (is_array($name)) {
            try {
                foreach ($name as $sessionName => $sessionValue) {
                    $SessionInstance->set($sessionName, $sessionValue);
                }
                return true;
            } catch (\Exception $exception) {
                return false;
            }
        } else {
            return $SessionInstance->set($name, $value);
        }
    }

    /**
     * Get Session
     * @param $name
     * @param $default
     * @author : evalor <master@evalor.cn>
     * @return mixed|null
     */
    static function find($name, $default = null)
    {
        $SessionInstance = Request::getInstance()->session();
        return $SessionInstance->get($name, $default);
    }

    /**
     * Check Session exists
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return bool
     */
    static function has($name)
    {
        return static::find($name, null) !== null;
    }

    /**
     * Delete Session Values
     * @param $name
     * @author : evalor <master@evalor.cn>
     * @return bool|int
     */
    static function delete($name)
    {
        $SessionInstance = Response::getInstance()->session();
        return $SessionInstance->set($name, null);
    }

    /**
     * Clear Session
     * @author : evalor <master@evalor.cn>
     */
    static function clear()
    {
        $Response = Response::getInstance();
        $SessionInstance = $Response->session();
        $SessionInstance->destroy();
        $Response->setCookie($SessionInstance->sessionName(), null, 0);
    }
}
```