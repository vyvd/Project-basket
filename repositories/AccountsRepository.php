<?php

require_once('BaseRepository.php');
require_once('CoursesAssignedRepository.php');


class AccountsRepository extends BaseRepository
{
    protected $tableName = "accounts";

    const COMMON_EMAIL_DOMAINS = [
        'gmail.com',
        'hotmail.com',
        'hotmail.co.uk',
        'googlemail.com',
        'yahoo.com',
        'yahoo.co.uk',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function fetchAccount($accountID)
    {
        return ORM::for_table($this->tableName)
            ->where('id', $accountID)
            ->findOne();
    }

    public function fetchDaysLoggedIn($accountID)
    {
        $result = ORM::for_table($this->tableName)
            ->select('loginDays')
            ->where('id', $accountID)
            ->findOne();
        $loginDays = $result->get('loginDays');
        return ($loginDays) ?: 0;
    }

    public function fetchAssignedCourses(?string $dateFrom = null, ?string $dateTo = null, ?array $extraFields = [])
    {
        if (!$dateFrom) {
            $dateFrom = date('Y-m-d H:i:s', strtotime('-12 Months'));
        }
        if (!$dateTo) {
            $dateTo = date('Y-m-d H:i:s', time());
        }
        $coursesAssigned = new CoursesAssignedRepository();
        $coursesAssignedTable = $coursesAssigned->getTableName();
        $this->setCustomTableName($coursesAssignedTable);

        $fields = ["{$this->tableName}.*"];
        if (count($extraFields)) {
            $fields = $extraFields;
        }
        if (!count($this->select)) {
            $this->setSelect($fields);
        }
        if (!$this->where) {
            $this->setWhere("{$coursesAssignedTable}.whenAssigned >= :dateFrom AND {$coursesAssignedTable}.whenAssigned <= :dateTo");
        }
        if (!count($this->whereData)) {
            $this->setWhereData([
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]);
        }
        if (!count($this->joins)) {
            $this->setJoins([
                [
                    'join_table' => $this->getTableName(),
                    'join_table_column' => 'id',
                    'constraint' => '=',
                    'right_table' => $coursesAssignedTable,
                    'right_table_column' => 'accountID',
                ]
            ]);
        }
        if (!$this->groupBy) {
            $this->setGroupBy("{$coursesAssignedTable}.accountID");
        }

        $query = $this->fetchWithJoinsQuery();
        $results = $query->findMany();
        $this->cleanup();
        return $this->resultsHandler($results, $fields);
    }

    public function fetchBusinessAccounts(?int $limit = null, ?int $offset = 0)
    {

        require_once('CoursesRepository.php');
        $coursesRepo = new CoursesRepository();

        $coursesTbl = $coursesRepo->getTableName();
        $accountsTbl = $this->getTableName();

        $coursesAssignedRepo = new CoursesAssignedRepository();
        $coursesAssignedTbl = $coursesAssignedRepo->getTableName();

        $coursesAssignedRepo->setSelect([
            "{$accountsTbl}.id",
            "{$coursesAssignedTbl}.id as courseAssignedId",
            "{$coursesTbl}.id as courseID",
            "{$accountsTbl}.firstname",
            "{$accountsTbl}.lastname",
            "{$accountsTbl}.email",
            "{$accountsTbl}.whenCreated",
            "{$coursesAssignedTbl}.whenAssigned as whenAssigned",
        ]);

        $ignoredDomainsWhereItems = array_map(function ($domain) {
            return "{$this->getTableName()}.email NOT LIKE '%{$domain}%'";
        }, self::COMMON_EMAIL_DOMAINS);

        $ignoredDomainsWhereData = implode(' AND ', $ignoredDomainsWhereItems);

        $coursesAssignedRepo->setWhere("{$coursesTbl}.id AND {$ignoredDomainsWhereData}");

        $coursesAssignedRepo->setOrderBy("{$coursesAssignedTbl}.whenAssigned desc");
        $coursesAssignedRepo->setGroupBy("{$coursesAssignedTbl}.whenAssigned");
        if (is_int($limit)) {
            $coursesAssignedRepo->setLimit($limit);
        }
        $coursesAssignedRepo->setOffset($offset);
        $fetchCourseAccountRels = $coursesAssignedRepo->fetchCourseAccountRelation(['title']);
        $this->cleanup();
        return $fetchCourseAccountRels;
    }
}