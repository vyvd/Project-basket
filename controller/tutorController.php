<?php

require_once(__DIR__ . '/courseController.php');

class tutorController extends Controller {

    /**
     * @var courseController
     */
    protected $course;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;

        $this->course = new courseController();
    }

    public function getTotalStudents()
    {
        return ORM::for_table("accounts")
            ->where("tutorID", CUR_ID_FRONT)
            ->where('isTutor', '0')
            ->count();
    }

    public function getStudents($limit = null)
    {
        $students = ORM::for_table("accounts")
            ->where("tutorID", CUR_ID_FRONT)
            ->where('isTutor', '0')
            ->order_by_desc('id');
        if($limit){
            $students = $students->limit($limit);
        }
        $students = $students->find_many();
        return $students;
    }

    public function isValidStudent($studentID)
    {
        $student = ORM::for_table('accounts')
            ->where('tutorID', CUR_ID_FRONT)->find_one($studentID);
        return $student ?? false;
    }

    public function getAssignments($isCount = false, $status = null)
    {
        $students = $this->getStudents();
        $studentIDs = [];

        if(count($students) >= 1) {
            foreach ($students as $student) {
                $studentIDs[] = $student->id;
            }
            $assignments = ORM::for_table('accountAssignments')
                ->where_in('accountID', $studentIDs)
                ->where_not_equal('status', 0)
                ->order_by_desc('updated_at');
            if($status){
                $assignments = $assignments->where('status', $status);
            }

            if($isCount){
                return $assignments->count();
            }
            return $assignments->find_many();

        }
        return null;


    }

    public function getPendingAssignmentsCount($isCount = false, $status = null)
    {
        return $this->getAssignments(true, 1);
    }

    public function accessUserAccount() {

        $account = ORM::For_table("accounts")->find_one(CUR_ID_FRONT);

        if($account->isIQA != "1") {
            exit;
        }

        $user = ORM::for_table("accounts")->where_any_is(array(
                array('isNCFE' => '1'),
                array('isTutor' => '1'))
            )
            ->find_one($this->get["id"]);

        if ($user->id == "") {
            exit;
        }

        $_SESSION["adminAccessed"] = "yes";
        $_SESSION["iqaAccessed"] = "yes";
        $_SESSION["iqaID"] = CUR_ID_FRONT;

        $newSignedID = $user->id;
        $_SESSION['id_front'] = $newSignedID;

        $_SESSION['idx_front']
            = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");

        //Added by Zubaer
        $_SESSION['nsa_email_front'] = $user->email;

        $_SESSION['csrftoken'] = substr(base_convert(sha1(uniqid(mt_rand())),
            16, 36), 0, 40);


        header('Location: '.SITE_URL.'dashboard');

    }

    public function exitIqaAccess()
    {

        unset($_SESSION["adminAccessed"]);
        unset($_SESSION["iqaAccessed"]);
        unset($_SESSION['idx_front']);

        $newSignedID = $_SESSION["iqaID"];
        $_SESSION['id_front'] = $newSignedID;

        $_SESSION['idx_front']
            = base64_encode("g4p3h9xfn8sq03hs2234$newSignedID");
        $_SESSION['csrftoken'] = substr(base_convert(sha1(uniqid(mt_rand())),
            16, 36), 0, 40);

        header('Location: '.SITE_URL.'dashboard');

    }
}