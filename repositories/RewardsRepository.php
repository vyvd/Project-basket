<?php
require_once('BaseRepository.php');
require_once('AccountsRepository.php');

class RewardsRepository extends BaseRepository
{
    protected $tableName = "rewards";

    protected $accountsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->accountsRepository = new AccountsRepository();
    }

    public function fetchInCompletedCourses(array $fields, $accountID, ?int $limit = null)
    {
        $coursesTableName = (new CoursesRepository())->getTableName();

        $this->setSelect($fields);
        $this->setWhere('completed <> :completed AND accountID = :accountID');
        $this->setWhereData([
            'completed' => '1',
            'accountID' => $accountID
        ]);
        $this->setJoins([
            [
                'join_table' => $coursesTableName,
                'join_table_column' => 'id',
                'constraint' => '=',
                'right_table' => $this->tableName,
                'right_table_column' => 'courseID',
            ]
        ]);
        $this->setLimit($limit);
        $query = $this->fetchWithJoinsQuery();
        $results = $query->findMany();
        return $this->buildResultsArray($results, $fields);
    }

    public function fetchActiveCoursesCount($accountID)
    {
        return ORM::for_table($this->tableName)
            ->select('activated')
            ->where('activated', '1')
            ->where('accountID', $accountID)
            ->count();
    }

    public function fetchHighestAccountRewardPoints(array $extraFields = [])
    {
        $this->setCustomTableName($this->accountsRepository->getTableName());
        $fields = array_merge(['rewardPoints'], $extraFields);
        $results = $this->fetchByParamsQuery($fields)
            ->orderByDesc('rewardPoints')
            ->findOne();
        $this->cleanup();
        return $results;
    }

    public function fetchRewardPointsByAccount($accountId, array $extraFields = [])
    {
        $accountRewardPoints = $this->accountsRepository->fetchByParamsQuery(
            array_merge(['rewardPoints'], $extraFields),
            ['id' => $accountId]
        )->findOne();
        if (!$accountRewardPoints instanceof ORM) {
            return false;
        }
        $this->setCustomTableName($this->accountsRepository->getTableName());
        $fields = array_merge(['rewardPoints'], $extraFields);
        $lowerResults = $this->fetchByParamsQuery($fields)
            ->whereLte('rewardPoints', $accountRewardPoints->get('rewardPoints'))
            ->whereNotEqual('id', $accountId)
            ->orderByDesc('rewardPoints')
            ->limit(2)
            ->findMany();
        $higherResults = $this->fetchByParamsQuery($fields)
            ->whereGte('rewardPoints', $accountRewardPoints->get('rewardPoints'))
            ->whereNotEqual('id', $accountId)
            ->orderByAsc('rewardPoints')
            ->limit(1)
            ->findMany();
        $this->cleanup();

        return array_merge(
            $higherResults,
            [$accountRewardPoints],
            $lowerResults
        );
    }
    public function fetchAccountRewardPoints($accountId, array $extraFields = [])
    {
        $results = $this->accountsRepository->fetchByParamsQuery(
            array_merge(['rewardPoints'], $extraFields),
            ['id' => $accountId]
        )->findOne();
        return ($results instanceof ORM)? $results->get('rewardPoints') : false;
    }
}