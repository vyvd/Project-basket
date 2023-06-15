<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseModuleProgressRepository extends BaseRepository
{
    protected $tableName = "courseModuleProgress";

    public function __construct()
    {
        parent::__construct();
    }
}