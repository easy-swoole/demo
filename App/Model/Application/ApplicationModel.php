<?php

namespace App\Model\Application;

/**
 * Class ApplicationModel
 * Create With Automatic Generator
 * @property $appId int |
 * @property $appName string |
 */
class ApplicationModel extends \EasySwoole\ORM\AbstractModel
{
	protected $tableName = 'application_list';


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

