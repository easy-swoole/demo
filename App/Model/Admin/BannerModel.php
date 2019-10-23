<?php

namespace App\Model\Admin;

use EasySwoole\ORM\AbstractModel;

/**
 * Class BannerModel
 * Create With Automatic Generator
 * @property $bannerId
 * @property $bannerImg
 * @property $bannerUrl
 * @property $state
 */
class BannerModel extends AbstractModel
{
    protected $tableName = 'banner_list';

    protected $primaryKey = 'bannerId';


    public function getAll(int $page = 1,int $state=1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['bannerUrl'] = ['%' . $keyword . '%','like'];
        }
        $where['state'] = $state;
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }


}