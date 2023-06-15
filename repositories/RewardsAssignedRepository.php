<?php
require_once('BaseRepository.php');
require_once('RewardsRepository.php');

class RewardsAssignedRepository extends RewardsRepository
{
    protected $tableName = "rewardsAssigned";
    private $rewardsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->rewardsRepository = new RewardsRepository();
    }
    public function fetchClaimedRewards(array $fields, $accountID, ?int $limit = null)
    {
        return $this->fetchRewardsClaimStatus(true, $fields, $accountID, $limit);
    }

    public function fetchUnclaimedRewards(array $fields, $accountID, ?int $limit = null)
    {
        return $this->fetchRewardsClaimStatus(false, $fields, $accountID, $limit);
    }

    public function fetchRewards($accountID, ?int $limit = null) {
        $rewardsTableName = $this->rewardsRepository->getTableName();
        $this->setSelect($this->select);
        $this->setWhere($this->where);
        $this->setWhereData(array_merge(['userID' => $accountID], $this->whereData));
        $this->setJoins([
            [
                'join_table' => $rewardsTableName,
                'join_table_column' => 'id',
                'constraint' => '=',
                'right_table' => $this->tableName,
                'right_table_column' => 'rewardID',
            ]
        ]);
        $this->setLimit($limit);
        $query = $this->fetchWithJoinsQuery();
        $results = $query->findMany();

        $results = $this->resultsHandler($results, $this->select);

        //Reset the class data
        $this->cleanup();
        return $results;
    }

    public function fetchRewardsClaimStatus(bool $claimStatus, array $extraFields, $accountID, ?int $limit = null)
    {
        if ($claimStatus) {
            $where = 'claimed = \'1\' AND userID = :userID';
        } else {
            $where = 'claimed <> \'1\' AND userID = :userID';
        }
        $this->setWhere($where);

        $this->setSelect(array_merge(['claimed', "{$this->rewardsRepository->getTableName()}.name as name"], $extraFields));
        return $this->fetchRewards($accountID);
    }

    public function fetchAccountRewards($accountId) {
        $rewardsTablename = $this->rewardsRepository->getTableName();
        $this->setSelect([
            "{$rewardsTablename}.id as id",
            'claimed',
            "{$rewardsTablename}.name as name",
            "{$rewardsTablename}.short as short"
        ]);
        $this->setWhere('userID = :userID');
        return $this->fetchRewards($accountId);
    }
    /**
     * @return RewardsRepository
     */
    public function getRewardsRepository(): RewardsRepository
    {
        return $this->rewardsRepository;
    }
}