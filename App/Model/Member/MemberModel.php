<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/28
 * Time: 11:47 AM
 */

namespace App\Model\Member;


use App\Model\BaseModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\SnowFlake;

class MemberModel extends BaseModel
{
    protected $table = 'member';

    function getAll($condition = [], int $page = 1, $pageSize = 10): array
    {
        $allow = ['where', 'orWhere', 'join', 'orderBy', 'groupBy'];
        foreach ($condition as $k => $v) {
            if (in_array($k, $allow)) {
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }
        $list = $this->getDb()
            ->withTotalCount()
            ->orderBy('member_id', 'DESC')
            ->get($this->table, [$pageSize * ($page - 1), $pageSize]);
        $total = $this->getDb()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    function getOne(MemberBean $userBean): ?MemberBean
    {
        $user = $this->getDb()
            ->where('member_id', $userBean->getMemberId())
            ->getOne($this->table);
        if (empty($user)) {
            return null;
        }
        return new MemberBean($user);
    }

    function update(MemberBean $memberBean, array $data)
    {
        $this->getDb()->where('member_id', $memberBean->getMemberId())->update($this->table, $data);
        return $this->getDb()->getAffectRows();
    }

    function register(MemberBean $bean)
    {
        return $this->getDb()->insert($this->table, $bean->toArray());
    }

    function delete(MemberBean $bean)
    {
        return $this->getDb()->where('member_id', $bean->getMemberId())->delete($this->table);
    }
}