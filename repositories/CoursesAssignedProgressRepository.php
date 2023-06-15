<?php
require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CoursesAssignedProgressRepository extends CoursesRepository
{
    protected $tableName = "coursesAssigned";

    public function __construct()
    {
        parent::__construct();
    }
}