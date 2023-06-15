<?php
class cacheController extends Controller {

    public function cacheUrl($request) {

        $contents = file_get_contents(SITE_URL.$request);

        $cachefile = TO_PATH_CDN.'cache/cached-'.str_replace("/", "", $request).'index'.$_SESSION["refCodeInternal"].'.html';

        $cached = fopen($cachefile, 'w');
        fwrite($cached, $contents);
        fclose($cached);



    }

    public function generateCoursesCache() {

        $courses = ORM::for_table("courses")->find_many();

        foreach($courses as $course) {

            $this->cacheUrl('course/'.$course->slug);

        }

    }

    public function generateCourseSingleCache($id) {

        $course = ORM::for_table("courses")->find_one($id);

        $this->cacheUrl('course/'.$course->slug);

    }

}