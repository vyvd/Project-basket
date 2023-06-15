<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');
require_once('AccountsRepository.php');

class CoursesAssignedRepository extends BaseRepository
{
    protected $tableName = "coursesAssigned";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Runs a db fetch for courseAssigned data with relations/joins to courses and accounts
     * to speed up db fetches when adding joins/relations
     *
     * @param array|null $courseFields
     * @param array|null $accountFields
     * @return array|array[]
     */
    public function fetchCourseAccountRelation(?array $courseFields = [], ?array $accountFields = []) {
        $coursesRepo = new CoursesRepository();
        $accountsRepo = new AccountsRepository();
        $accountsTbl = $accountsRepo->getTableName();
        $coursesAssignedTbl = $this->getTableName();
        $coursesTbl = $coursesRepo->getTableName();

        //Add courseID col alias to select array, if it does not already exist
        if (!in_array("{$coursesTbl}.id as courseID", $this->select)) {
            $this->select[] = "{$coursesTbl}.id as courseID";
        }

        //Prepend any account columns with the table name for the select array
        foreach ($accountFields as $accountField) {
            if (strpos($accountField, "{$accountsRepo->getTableName()}.") === false) {
                $accountField = "{$accountsRepo->getTableName()}.{$accountField}";
            }
            $this->select[] = $accountField;
        }

        //Search joins array for courses join
        //Add join, if it doesn't already exist
        $findCourseJoinIndex = array_search($coursesTbl, array_column($this->joins, 'join_table'));
        if ($findCourseJoinIndex === false || $this->joins[$findCourseJoinIndex]['join_table_column'] !== 'id') {
            $this->joins[] = [
                'join_table' => $coursesRepo->getTableName(),
                'join_table_column' => 'id',
                'constraint' => '=',
                'right_table' => $coursesAssignedTbl,
                'right_table_column' => 'courseID',
            ];
        }
        //Search joins array for accounts join
        //Add join, if it doesn't already exist
        $findAccountJoinIndex = array_search($accountsTbl, array_column($this->joins, 'join_table'));
        if ($findAccountJoinIndex === false || $this->joins[$findAccountJoinIndex]['join_table_column'] !== 'id') {
            $this->joins[] = [
                'join_table' => (new AccountsRepository())->getTableName(),
                'join_table_column' => 'id',
                'constraint' => '=',
                'right_table' => $coursesAssignedTbl,
                'right_table_column' => 'accountID',
            ];
        }

        //Run db fetch
        $query = $this->fetchWithJoinsQuery();
        $results = $query->findMany();
        if (!count($results)) {
            return [];
        }

        //Build an array of the course IDs from the results
        $getCourseIDs = $this->repositoryHelpers::buildOrmResultsValueArray('courseID', $results, true);

        //Set the resultFormat() to change ORM results into arrays in the resultHandler() function
        $this->setResultFormat(parent::RESULT_FORMAT_TO_ARRAY);
        $results = $this->resultsHandler($results, $this->select);

        //Reset the class data
        $this->cleanup();

        //Set the resultFormat() to change ORM results into arrays in the resultHandler() function
        $coursesRepo->setResultFormat(parent::RESULT_FORMAT_TO_ARRAY);

        //Adds course columns passed to this function
        $coursesRepo->setSelect(array_merge(['id'], $courseFields));

        //Fetch courses with the id's passed
        $fetchCourses = $coursesRepo->fetchCoursesByIds($getCourseIDs);

        //Sort the courses by the same order of the original id arrays
        $sortCoursesByIdArray = $this->repositoryHelpers::sortResultsSetByArrayValues(
            'id', $fetchCourses, $getCourseIDs
        );

        if (!$sortCoursesByIdArray) {
            $sortCoursesByIdArray = [];
        }

        //Merges the courses to the courseAssigned results
        $buildResults = array_map(function ($course) use ($results) {
            $findCourseIdIndex = array_search($course['id'], array_column($results, 'courseID'));
            if ($findCourseIdIndex === false) {
                return $course;
            }
            return array_merge($course, $results[$findCourseIdIndex]);
        }, $sortCoursesByIdArray);


        $this->cleanup();
        return $buildResults;
    }
}