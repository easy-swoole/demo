<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/3/8 0008
 * Time: 15:16
 */

namespace App\HttpController;


use App\Utility\Excel;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
       $arr = [
           ['name'=>'仙士可1号','value'=>1],
           ['name'=>'仙士可2号','value'=>2],
           ['name'=>'仙士可3号','value'=>3],
           ['name'=>'仙士可4号','value'=>4],
           ['name'=>'仙士可5号','value'=>5],
           ['name'=>'仙士可6号','value'=>6],
           ['name'=>'仙士可7号','value'=>7],
           ['name'=>'仙士可8号','value'=>8],
       ];
       Excel::exportExcel($this->response(),$arr,['name','value']);
    }


}