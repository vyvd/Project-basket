<?php
// include other controllers to access their functionality
require_once(__DIR__ . '/mediaController.php');

class exampleController extends Controller
{

    /**
     * @var mediaController
     */
    protected $media;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->media = new mediaController();
    }

    public function test() {

        echo "This is a test";

    }

    public function courseFeed() {

        // get all courses
        $courses = ORM::for_table("courses")->find_many();

        foreach($courses as $course) {

            ?>
            <p><?= $course->title ?></p>
            <?php

        }

    }


}
