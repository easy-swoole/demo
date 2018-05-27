<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/3
 * Time: 下午6:19
 */

namespace App\HttpController\Api;


use App\Model\User\Bean;
use App\Utility\SysConst;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Utility\Validate\Rule;
use EasySwoole\Core\Utility\Validate\Rules;
use \App\Model\User\User as UserModel;

class Common extends AbstractBase
{
    /*
     * 测试url /api/common/register/index.html?account=test&password=123456
     */
    function register()
    {
        $rule = new Rules();
        $rule->add('account','account字段错误')->withRule(Rule::REQUIRED);
        $rule->add('password','password字段错误')->withRule(Rule::REQUIRED)
            ->withRule(Rule::MIN_LEN,6)
            ->withRule(Rule::MAX_LEN,16);
        $v = $this->validateParams($rule);
        if(!$v->hasError()){
            $bean = new Bean($v->getRuleData());
            $model = new UserModel();
            $ret = $model->register($bean);
            if($ret){
                $this->writeJson(Status::CODE_OK, [
                    'userId'=>$ret
                ],'注册成功');
            }else{
                $this->writeJson(Status::CODE_BAD_REQUEST, null,'注册失败，账户可能已经存在');
            }
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST,null,$v->getErrorList()->first()->getMessage());
        }
    }

    function login()
    {
        $rule = new Rules();
        $rule->add('account','account字段错误')->withRule(Rule::REQUIRED);
        $rule->add('password','password字段错误')->withRule(Rule::REQUIRED);
        $v = $this->validateParams($rule);
        if(!$v->hasError()){
            $bean = new Bean($v->getRuleData());
            $model = new UserModel();
            $ret = $model->login($bean);
            if($ret){
                $this->response()->setCookie(SysConst::COOKIE_USER_SESSION_NAME,$bean->getSession(),time()+SysConst::COOKIE_USER_SESSION_TTL);
                $this->writeJson(Status::CODE_OK,$bean->toArray());
            }else{
                $this->writeJson(Status::CODE_UNAUTHORIZED,null,'账户或密码错误');
            }
        }else{
            $this->writeJson(Status::CODE_BAD_REQUEST,null,$v->getErrorList()->first()->getMessage());
        }
    }
}