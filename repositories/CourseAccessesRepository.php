<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseAccessesRepository extends BaseRepository
{
    protected $tableName = "courseAccesses";

    public function __construct()
    {
        parent::__construct();
    }
}