<?php

namespace App\Model\Login;

/**
 * Class UserApplicationLoginModel
 * Create With Automatic Generator
 * @property $id int |
 * @property $appId int |
 * @property $userId int |
 * @property $appSecret string |
 * @property $expireTime int |
 */
class UserApplicationLoginModel extends \EasySwoole\ORM\AbstractModel
{
	protected $tableName = 'user_application_login_list';


	/**
	 * @getAll
	 * @keyword userAccount
	 * @param  int  $page  1
	 * @param  string  $keyword
	 * @param  int  $pageSize  10
	 * @param  string  $field  *
	 * @return array[total,list]
	 */
	public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10, string $field = '*'): array
	{
		if (!empty($keyword)) {
		    $this->where('userAccount', '%' . $keyword . '%', 'like');
		}
		$list = $this
		    ->withTotalCount()
			->order($this->schemaInfo()->getPkFiledName(), 'DESC')
		    ->field($field)
		    ->limit($pageSize * ($page - 1), $pageSize)
		    ->all();
		$total = $this->lastQueryResult()->getTotalCount();;
		return ['total' => $total, 'list' => $list];
	}
}

