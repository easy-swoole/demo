<?php

namespace App\HttpController\Api\Common;

use App\Model\Admin\BannerBean;
use App\Model\Admin\BannerModel;
use EasySwoole\Http\Message\Status;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Validate\Validate;

/**
 * Class Banner
 * Create With Automatic Generator
 */
class Banner extends CommonBase
{

	public function getOne()
	{
		$db = Mysql::defer('mysql');
		$param = $this->request()->getRequestParam();
		$model = new BannerModel($db);
		$bean = $model->getOne(new BannerBean(['bannerId' => $param['bannerId']]));
		if ($bean) {
		    $this->writeJson(Status::CODE_OK, $bean, "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
		}
	}

	public function getAll()
	{
        $db = Mysql::defer('mysql');
        $param = $this->request()->getRequestParam();
		$page = $param['page']??1;
		$limit = $param['limit']??20;
		$model = new BannerModel($db);
		$data = $model->getAllByState($page, 1,$param['keyword']??null, $limit);
		$this->writeJson(Status::CODE_OK, $data, 'success');
	}

    function getValidateRule(?string $action): ?Validate
    {
        $validate = null;
        switch ($action) {
            case 'getAll':
                $validate = new Validate();
                $validate->addColumn('page', '页数')->optional();
                $validate->addColumn('limit', 'limit')->optional();
                $validate->addColumn('keyword', '关键词')->optional();
                break;
            case 'getOne':
                $validate = new Validate();
                $validate->addColumn('bannerId', '主键id')->required()->lengthMax(11);
                break;
        }
        return $validate;
    }
}