<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CoursesSavedRepository extends BaseRepository
{
    protected $tableName = "coursesSaved";

    public function __construct()
    {
        parent::__construct();
    }
}