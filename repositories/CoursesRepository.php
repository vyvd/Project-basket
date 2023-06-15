<?php

require_once('BaseRepository.php');
require_once('CoursesAssignedRepository.php');
require_once('AccountsRepository.php');

class CoursesRepository extends BaseRepository
{
    protected $tableName = "courses";

    protected $accountsRepository;
    protected $coursesAssignedRepository;

    public function __construct()
    {
        parent::__construct();
        $this->accountsRepository = new AccountsRepository();
        $this->coursesAssignedRepository = new CoursesAssignedRepository();
    }

    public function fetchCompletedCoursesCount($accountID)
    {
        return ORM::for_table($this->coursesAssignedRepository->getTableName())
            ->select('completed')
            ->where('completed', '1')
            ->where('accountID', $accountID)
            ->count();
    }

    public function fetchInCompletedCourses(array $fields, $accountID, ?int $limit = null)
    {
        $courseAssignmentTable = $this->coursesAssignedRepository->getTableName();
        $coursesTableName = $this->getTableName();
        $this->setCustomTableName($courseAssignmentTable);

        $this->setSelect($fields);
        $this->setWhere("completed <> :completed AND accountID = :accountID AND {$coursesTableName}.title <> ''");
        $this->setWhereData([
            'completed' => '1',
            'accountID' => $accountID
        ]);
        $this->setJoins([
            [
                'join_table' => $coursesTableName,
                'join_table_column' => 'id',
                'constraint' => '=',
                'right_table' => $courseAssignmentTable,
                'right_table_column' => 'courseID',
            ]
        ]);
        $this->setLimit($limit);
        $this->setGroupBy("{$courseAssignmentTable}.courseID");
        $query = $this->fetchWithJoinsQuery();
        $results = $query->findMany();
        $this->cleanup();
        return $this->resultsHandler($results, $fields);
    }

    public function fetchActiveCoursesCount($accountID)
    {
        return ORM::for_table($this->coursesAssignedRepository->getTableName())
            ->select('activated')
            ->where('activated', '1')
            ->where('accountID', $accountID)
            ->count();
    }

    public function fetchMostPopularCourses(array $extraFields = [], ?int $limit = null)
    {

        require_once('CoursesRepository.php');
        $coursesAssignedRepo = new CoursesAssignedRepository();
        $coursesAssignedTbl = $this->coursesAssignedRepository->getTableName();

        $coursesAssignedRepo->setSelect(
            array_merge([
                'courses.id as courseID',
                'coursesAssigned.id as id',
                'count(coursesAssigned.courseID) as courseCount',
            ], $extraFields)
        );

        $coursesAssignedRepo->setOrderBy("courseCount desc");
        $coursesAssignedRepo->setGroupBy("{$coursesAssignedTbl}.courseID");
        if (is_int($limit)) {
            $coursesAssignedRepo->setLimit($limit + 10);
        }
        $fetchCourseAccountRels = $coursesAssignedRepo->fetchCourseAccountRelation(['title', 'slug']);
        $this->cleanup();

        return array_splice($fetchCourseAccountRels, 0, $limit);
    }

    public function fetchCoursesByIds(array $coursesIds = []) {

        if (!count($coursesIds)) {
            return [];
        }

        $fetchCoursesQuery = $this->getOrm();
        if (!in_array('id', $this->select)) {
            $fetchCoursesQuery->select('id');
        }
        foreach ($this->select as $column) {
            $fetchCoursesQuery->select($column);
        }
        $fetchCourses = $fetchCoursesQuery->where_in('id', $coursesIds)
            ->whereNotEqual('title', '')
            ->findMany();
        return $this->resultsHandler($fetchCourses, $this->select);
    }

    public function courseModelDuplicator($courseId, ?array $dbConnections = []) {

        require_once(APP_ROOT_PATH . 'repositories/helpers/ModelDuplicator.php');
        require_once(APP_ROOT_PATH . 'repositories/AccountAssignmentsRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/AccountDocumentsRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CoursesAssignedRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseAccessesRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseModuleProgressRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseNotesRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseRatingsRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseReviewsRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseTimeProgressRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CoursesSavedRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseModulesRepository.php');

        $ignoreTables = [
            (new AccountAssignmentsRepository())->getTableName(),
            (new AccountDocumentsRepository())->getTableName(),
            (new CoursesAssignedRepository())->getTableName(),
            (new CourseAccessesRepository())->getTableName(),
            (new CourseModuleProgressRepository())->getTableName(),
            (new CourseNotesRepository())->getTableName(),
            (new CourseRatingsRepository())->getTableName(),
            (new CourseReviewsRepository())->getTableName(),
            (new CourseTimeProgressRepository())->getTableName(),
            (new CoursesSavedRepository())->getTableName(),
        ];
        $courseModules = new CourseModulesRepository();
        $modelDuplicator = new ModelDuplicator();
        $modelDuplicator->setDbConnections($dbConnections);
        return $modelDuplicator->modelDuplicator(
            $this,
            $courseId,
            ['courseID', 'course_id'],
            $ignoreTables,
            function($tableName, $modelData, $dbConnection) {
                switch ($dbConnection) {
                    case 'csv_library':
                        unset($modelData['allowSecondaryName']);
                        break;
                }
                $modelData['title'] = "{$modelData['title']} Copy";
                $modelData['slug'] = "{$modelData['slug']}_copy";
                $modelData['enrollmentCount'] = 0;
                $modelData['hidden'] = '1';
                return $modelData;
            },
            function($tableName, $modelData, $dbConnection) use($courseModules) {
                if ($tableName === $courseModules->getTableName()) {
                    $modelData['title'] = "{$modelData['title']} Copy";
                    $modelData['slug'] = "{$modelData['slug']}_copy";
                }
                return $modelData;
            },
        );
    }
}