<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseNotesRepository extends BaseRepository
{
    protected $tableName = "courseNotes";

    public function __construct()
    {
        parent::__construct();
    }
}