<?php
//Get a single course

$course_title = $_GET['course_title'];

if(empty($course_title)) {
    die('Please provide a valid course_title');
}

$course_data = $this->controller->getCourseByTitle($course_title);