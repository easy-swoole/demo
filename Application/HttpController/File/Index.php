<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/3/15
 * Time: 下午3:14
 */

namespace App\HttpController\File;


use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $file = $this->request()->getUploadedFile('testFile');
        if($file){
//            $file->
        }else{
            $this->response()->write('you have not file');
        }
    }
}