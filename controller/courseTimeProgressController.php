<?php
class courseTimeProgressController extends Controller {

    public function __construct()
    {
        $this->table = 'courseTimeProgress';
        $this->post = $_POST;
        $this->get = $_GET;
    }

    public function track() {

        // this function is called every 10 seconds via ajax to track how long someone has spent on a course within a given day

        // get assigned record for current user
        $assigned = ORM::for_table("coursesAssigned")->where("accountID", CUR_ID_FRONT)->find_one($this->get["id"]);

        // current date
        $date = date('Y-m-d');

        if($assigned->id != "") {

            // if it exists, then start tracking progress
            $progress = ORM::for_table($this->table)
                ->where("assignedID", $assigned->id)
                ->where("date", $date)
                ->find_one();

            if($progress->id == "") {

                // create if it doesnt exist
                $progress = ORM::for_table($this->table)->create();

                $progress->assignedID = $assigned->id;
                $progress->accountID = CUR_ID_FRONT;
                $progress->set_expr("whenCreated", "NOW()");
                $progress->courseID = $assigned->courseID;
                $progress->date = $date;
                $progress->seconds = 0;

            }

            $progress->seconds = $progress->seconds+10;
            $progress->set_expr("lastUpdated", "NOW()");

            $progress->save();

        }

    }

    public function calculateCourseTimeAccountAjax() {


        // only admin users allowed
        if(CUR_ID != "") {
            $value = $this->calculateCourseTimeAccount($this->get["accountID"], $this->get["dateFrom"], $this->get["dateTo"], $this->get["assigned"]);
            echo number_format($value/3600, 2);
        }

    }

    public function calculateCourseTimeAccount($accountID, $dateFrom, $dateTo, $assignedID = "") {

        if($assignedID == "") {

            $hours = ORM::for_table($this->table)->raw_query('SELECT SUM(`seconds`) as seconds FROM `courseTimeProgress` WHERE date >= :dateFrom AND date <= :dateTo AND accountID = :accountID', array('dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'accountID' => $accountID))->find_many();

        } else {

            $hours = ORM::for_table($this->table)->raw_query('SELECT SUM(`seconds`) as seconds FROM `courseTimeProgress` WHERE date >= :dateFrom AND date <= :dateTo AND accountID = :accountID AND assignedID = :assignedID', array('dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'accountID' => $accountID, 'assignedID' => $assignedID))->find_many();

        }


        $seconds = 0;

        foreach($hours as $hour) {
            $seconds = $hour->seconds;
        }

        if($seconds == "") {
            $seconds = 0;
        }

        return $seconds;

    }

}