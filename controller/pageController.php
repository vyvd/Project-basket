<?php
class pageController extends Controller {

    public function getSinglePage($id = "") {

        if($id != "") {
            return ORM::for_table("pages")->find_one($id);
        } else {
            return ORM::for_table("pages")->where("slug", REQUEST)->find_one();
        }

    }

}