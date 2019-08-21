<?php

namespace App\Model\Admin;

/**
 * Class BannerModel
 * Create With Automatic Generator
 */
class BannerModel extends \App\Model\BaseModel
{
    protected $table = 'banner_list';

    protected $primaryKey = 'bannerId';


    /**
     * @getAll
     * @keyword bannerUrl
     * @param  int  page  1
     * @param  string  keyword
     * @param  int  pageSize  10
     * @return array[total,list]
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        if (!empty($keyword)) {
            $this->getDbConnection()->where('bannerUrl', '%' . $keyword . '%', 'like');
        }

        $list = $this->getDbConnection()
            ->withTotalCount()
            ->orderBy($this->primaryKey, 'DESC')
            ->get($this->table, [$pageSize * ($page  - 1), $pageSize]);
        $total = $this->getDbConnection()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /**
     * getAllByState
     * @param int         $page
     * @param int|null    $state
     * @param string|null $keyword
     * @param int         $pageSize
     * @return array
     * @throws \EasySwoole\Mysqli\Exceptions\ConnectFail
     * @throws \EasySwoole\Mysqli\Exceptions\Option
     * @throws \EasySwoole\Mysqli\Exceptions\OrderByFail
     * @throws \EasySwoole\Mysqli\Exceptions\PrepareQueryFail
     * @author Tioncico
     * Time: 15:13
     */
    public function getAllByState(int $page = 1, ?int $state = null, string $keyword = null, int $pageSize = 10): array
    {
        if (!empty($keyword)) {
            $this->getDbConnection()->where('bannerUrl', '%' . $keyword . '%', 'like');
        }
        if ($state!==null) {
            $this->getDbConnection()->where('state', $state);
        }

        $list = $this->getDbConnection()
            ->withTotalCount()
            ->orderBy($this->primaryKey, 'DESC')
            ->get($this->table, [$pageSize * ($page  - 1), $pageSize]);
        $total = $this->getDbConnection()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }


    /**
     * 默认根据主键(bannerId)进行搜索
     * @getOne
     * @param  BannerBean $bean
     * @return BannerBean
     */
    public function getOne(BannerBean $bean): ?BannerBean
    {
        $info = $this->getDbConnection()->where($this->primaryKey, $bean->getBannerId())->getOne($this->table);
        if (empty($info)) {
            return null;
        }
        return new BannerBean($info);
    }


    /**
     * 默认根据bean数据进行插入数据
     * @add
     * @param  BannerBean $bean
     * @return bool
     */
    public function add(BannerBean $bean): bool
    {
        return $this->getDbConnection()->insert($this->table, $bean->toArray(null, $bean::FILTER_NOT_NULL));
    }


    /**
     * 默认根据主键(bannerId)进行删除
     * @delete
     * @param  BannerBean $bean
     * @return bool
     */
    public function delete(BannerBean $bean): bool
    {
        return  $this->getDbConnection()->where($this->primaryKey, $bean->getBannerId())->delete($this->table);
    }


    /**
     * 默认根据主键(bannerId)进行更新
     * @delete
     * @param  BannerBean $bean
     * @param  array $data
     * @return bool
     */
    public function update(BannerBean $bean, array $data): bool
    {
        if (empty($data)){
            return false;
        }
        return $this->getDbConnection()->where($this->primaryKey, $bean->getBannerId())->update($this->table, $data);
    }
}