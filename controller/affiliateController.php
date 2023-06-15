<?php

require_once(__DIR__ . '/courseController.php');
require_once(__DIR__ . '/courseCategoryController.php');

class affiliateController extends Controller
{
    /**
     * @var courseController
     */
    protected $courses;

    /**
     * @var courseCategoryController
     */
    protected $courseCategories;

    public function __construct()
    {
        $this->courses = new courseController();
        $this->courseCategories = new courseCategoryController();

    }

    public function updateIframes()
    {
        //ORM::configure('ID', 'primary_key');
        $iframes = ORM::for_table('ap_iframe_generator')
            ->where('oldCourseID', 1)
            ->find_many();

        if($iframes){
            foreach ($iframes as $iframe){

                $courses = @$iframe->courses ? json_decode($iframe->courses) : null;
                if(count($courses) >= 1){
                    $newCourses = [];
                    foreach ($courses as $course){
                        $c = explode("_",$course);
                        if(@$c[0]){
                            $newCourse = $this->courses->getCourseByOldID($c[0]);
                            $c[0] = $newCourse->id;
                        }
                        if(@$c[1]){
                            $newCategory = $this->courseCategories->getCategoryByOldID($c[1]);
                            $c[1] = $newCategory->id;
                        }
                        $newCourses[] = implode('_', $c);
                    }
                    $ncourses = is_array($newCourses) ? json_encode($newCourses): null;
                    $sql = "Update ap_iframe_generator set courses='". $ncourses ."', oldCourseID=0 where ID='".$iframe->ID."'";
                    ORM::raw_execute($sql);
                }
            }
        }
        echo "Complete";
        exit();
    }
}