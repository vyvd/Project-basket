<?php

require_once(APP_ROOT_PATH . 'repositories/RewardsAssignedRepository.php');
class RewardHelpers
{
    private $rewardsAssignedRepo;
    private $rewardsRepo;

    public function __construct()
    {
        $this->rewardsAssignedRepo = new RewardsAssignedRepository();
        $this->rewardsRepo = $this->rewardsAssignedRepo->getRewardsRepository();
    }

    public function buildAccountRewardsQueryData($accountId) {
        $this->rewardsAssignedRepo->setResultFormat($this->rewardsAssignedRepo::RESULT_FORMAT_RAW);
        $accountRewards = $this->rewardsAssignedRepo->fetchAccountRewards($accountId);
        $rewards = $this->rewardsAssignedRepo->getRewardsRepository()->fetchAll(['name']);
        if (!count($accountRewards) || !count($rewards)) {
            return false;
        }
        $rewardCustomFields = array_map(function (ORM $reward) {
            return "Reward: {$reward->get('name')}";
        }, $rewards);
        $rewardCustomFields = array_fill_keys($rewardCustomFields, 'Not claimed');
        foreach ($accountRewards as $accountReward) {
            $rewardName = $accountReward->get('name');
            $rewardClaimed = $accountReward->get('claimed');
            if (!$rewardName || !(int)$rewardClaimed) {
                continue;
            }
            $rewardCustomFields["Reward: {$rewardName}:"] = 'Claimed';
        }
        return http_build_query($rewardCustomFields);
    }
}