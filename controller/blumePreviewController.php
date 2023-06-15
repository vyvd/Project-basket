<?php

class blumePreviewController extends Controller
{

    /*
     * ALTER TABLE `courseModuleHistory` ADD `preview` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `adminID`;
     */


    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }
    }

    public function saveModulePreview() {

        // save preview into history
        $item = ORM::for_table("courseModules")
            ->find_one($this->post["itemID"]);

        $history = ORM::for_table("courseModuleHistory")->create();

        $history->moduleID = $this->post["itemID"];
        $history->title = $item->title;
        $history->slug = $item->slug;
        $history->description = $item->description;
        $history->contents = $item->contents;
        $history->adminID = CUR_ID;
        $history->set_expr("whenSaved", "NOW()");
        $history->preview = "1";

        $history->save();


    }

    public function loadModulePreview() {



    }

}