<?php
require_once('BaseRepository.php');

class CourseTimeProgressRepository extends BaseRepository
{
    protected $tableName = "courseTimeProgress";

    public function __construct()
    {
        parent::__construct();
    }

    public function calculateCourseTimeAccount($accountID, $dateFrom, $dateTo) {
        $getCourseTimes = ORM::for_table($this->tableName)
            ->select('seconds')
            ->where('accountID', $accountID)
            ->where_gte('date', $dateFrom)
            ->where_lte('date', $dateTo)
            ->findMany();
        $buildSecondsData = array_map(function ($row) {
            return (int)$row->seconds;
        }, $getCourseTimes);

        $seconds = array_sum($buildSecondsData);
        return [
            'hours' => floor($seconds / 3600),
            'minutes' => floor(($seconds / 60) % 60),
            'seconds' => $seconds
        ];
    }
}