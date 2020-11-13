<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Validate\Validate;

class Test extends Controller
{
    public function index()
    {
        $this->response()->write('test index');
        // TODO: Implement index() method.
    }

    public function user()
    {
        //记录输出错误
        Trigger::getInstance()->error('test error');
        $this->response()->write('user');
    }

    /**
     * param验证测试
     * @link https://github.com/easy-swoole/validate
     */
    public function param()
    {
        // http://127.0.0.1:9501/test/param/?test=1
        // http://127.0.0.1:9501/test/param/
        $validate = new Validate();
        $validate->addColumn('test')->required('必须传')->notEmpty('不能为空');

        // 注解验证器 看Annotation控制器
        $ret = $validate->validate($this->request()->getQueryParams());
        if ($ret == false) {
            $this->writeJson(200, [], $validate->getError()->__toString());
        }
    }
}
