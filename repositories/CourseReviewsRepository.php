<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseReviewsRepository extends BaseRepository
{
    protected $tableName = "courseReviews";

    public function __construct()
    {
        parent::__construct();
    }
}