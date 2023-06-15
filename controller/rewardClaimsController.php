<?php

class rewardClaimsController extends Controller {

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $tableCoupon;

    public function __construct()
    {
        $this->table = 'rewardsClaims';
        $this->tableCoupon = 'coupons';
    }

    public function getByPoints($points) {

        return ORM::for_table($this->table)->where_lte('points', $points)->order_by_desc('points')->find_one();

    }
    public function getAllByPoints($points) {

        return ORM::for_table($this->table)->where_lte('points', $points)->find_many();

    }

    public function getUserClaimedRewards($accountID)
    {
        $rewards = [];

        $userClaimedRewards = ORM::for_table($this->table)
            ->raw_query('SELECT r.id, c.code, c.totalUses, c.totalLimit FROM `rewardsClaims` r left JOIN coupons c ON r.id = c.rewardClaimID WHERE c.forUser = :accountID order by r.id', array('accountID' => $accountID))
            ->find_array();

        foreach ($userClaimedRewards as $reward){
            $rewards[$reward['id']]['code'] = $reward['code'];
            $rewards[$reward['id']]['totalUses'] = $reward['totalUses'];
            $rewards[$reward['id']]['totalLimit'] = $reward['totalLimit'];
        }

        return $rewards;
    }

    public function getAll()
    {
        $rewards = ORM::for_table($this->table)
            ->orderByAsc('id')
            ->find_many();

        return $rewards;
    }

}