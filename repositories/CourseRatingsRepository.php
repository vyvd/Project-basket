<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseRatingsRepository extends BaseRepository
{
    protected $tableName = "courseRatings";

    public function __construct()
    {
        parent::__construct();
    }
}