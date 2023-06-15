<?php
require_once(__DIR__ . '/mediaController.php');

class accountDocumentController extends Controller {

    /**
     * @var mediaController
     */
    protected $media;

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;

        $this->table = "accountDocuments";

        // if user is not logged in then dont let them do anything
        if (CUR_ID_FRONT == "") {
            header('Location: '.SITE_URL.'blume/login');
            exit;
        }

        $this->media = new mediaController();
    }

    public function getDocuments() {

        return ORM::for_table($this->table)->where("accountID", CUR_ID_FRONT)->order_by_desc("id")->find_many();

    }

    public function getSupportingDocuments()
    {
        $coursesAssigned = ORM::for_table("coursesAssigned")
            ->where("accountID", CUR_ID_FRONT)
            ->where("isNCFE", '1')
            ->select('courseID')
            ->find_many();
        $supportingDocuments = [];
        if(count($coursesAssigned)){
            foreach ($coursesAssigned as $course) {
                $courseIDs[] = $course->courseID;
            }
            $supportingDocuments = ORM::for_table('courseDocuments')
                ->where('audience', 's')
                ->where_in('courseID', $courseIDs)
                ->order_by_desc('whenAdded')
                ->find_many();
        }
        return $supportingDocuments;
    }


}
