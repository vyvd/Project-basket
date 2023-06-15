<?php

require_once(__DIR__ . '/rewardClaimsController.php');

class couponController extends Controller
{


    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $couponUserTable;

    /**
     * @var rewardClaimsController
     */
    protected $rewardClaims;

    public function __construct()
    {
        $this->table = 'coupons';
        $this->couponUserTable = 'couponUserIDs';
        $this->rewardClaims = new rewardClaimsController();
    }

    public function getCouponByID($id)
    {
        return ORM::for_table($this->table)->find_one($id);
    }

    public function getCouponByOldID($oldId)
    {
        return ORM::for_table($this->table)
            ->where('oldId', $oldId)
            ->find_one();
    }

    public function saveCoupon(array $input, array $usedBy = null)
    {
        $item = '';
        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
        } else if(@$input['forUser'] && @$input['rewardClaimID']){
            $item = ORM::for_table($this->table)
                ->where('rewardClaimID', $input['rewardClaimID'])
                ->where('forUser', $input['forUser'])
                ->find_one();
        }

        if(empty($item)){
            $input["whenAdded"] = isset($input["whenAdded"]) ? $input["whenAdded"] : date("Y-m-d H:i:s");
            $item = ORM::for_table($this->table)->create();
        }


        $data = $input;

        $item->set($data);
        $item->save();

        if(count($usedBy) >= 0){
            foreach ($usedBy as $a){
                $usedItem = ORM::for_table($this->couponUserTable)
                    ->where('couponID' , $item->id)
                    ->where('userID', $a)
                    ->find_one();
                if(empty($usedItem)){
                    $usedItem = ORM::for_table($this->couponUserTable)->create();
                }
                $usedData['couponID'] = $item->id;
                $usedData['userID'] = $a;
                $usedItem->set($usedData);
                $usedItem->save();
            }
        }

        // redirect to edit course so modules, etc can be added
        return $item;
    }

    protected function generateCouponCode()
    {
        $code = 'N'. rand(99,999) . 'SA' . time() . rand(99,999);
        return $code;
    }

    public function generateRewardCoupon($accountID, $points, $totalUses = 0) {

        //$claim = $this->rewardClaims->getByPoints($points);
        $claims = $this->rewardClaims->getAllByPoints($points);
        if(count($claims) >= 1){
            foreach ($claims as $claim){
                // Check if already exist
                $coupon = ORM::for_table('coupons')
                    ->where('type', $claim->type)
                    ->where('rewardClaimID', $claim->id)
                    ->where('forUser', $accountID)
                    ->find_one();
                if(empty($coupon)){
                    $data = [
                        'code' => $this->generateCouponCode(),
                        'type' => $claim->type,
                        'value' => $claim->value,
                        'totalUses' => $totalUses,
                        'totalLimit' => 1,
                        'expiry' => null,
                        'isReward' => 1,
                        'rewardClaimID' => $claim->id,
                        'forUser' => $accountID,
                        'whenAdded' => date("Y-m-d H:i:s"),
                        'whenUpdated' => date("Y-m-d H:i:s"),
                    ];
                    $this->saveCoupon($data);
                }
            }
        }

    }

    public function checkUserRewardCoupons($accountID, $points)
    {
        $rewards = $this->rewardClaims->getAllByPoints($points);
        if(count($rewards) >= 1){
            foreach ($rewards as $reward){
                // Check if already have Coupon
                $coupon = ORM::for_table($this->table)
                    ->where('isReward', 1)
                    ->where('rewardClaimID', $reward->id)
                    ->where('forUser', $accountID)
                    ->find_one();
                if(empty($coupon)){
                    $this->generateRewardCoupon($accountID, $reward->points);
                }
            }
        }
    }

}