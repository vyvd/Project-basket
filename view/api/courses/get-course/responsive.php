<?php
//Get a single course

$course_id = $_GET['course_id'];

if(empty($course_id)) {
    die('Please provide a course id');
}

$course_data = $this->controller->getCourse($course_id);