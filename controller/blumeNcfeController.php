<?php
require_once(__DIR__ . '/mediaController.php');
require_once(__DIR__ . '/blumeNewController.php');

class blumeNcfeController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    /**
     * @var blumeNewController
     */
    protected $blumeNew;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->medias = new mediaController();
        $this->blumeNew = new blumeNewController();

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: '.SITE_URL.'blume/login');
            exit;
        }

    }

    public function createTutor() {

        $item = ORM::For_table("accounts")->create();

        $item->isTutor = "1";
        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->set_expr("whenCreated", "NOW()");
        $item->set_expr("whenUpdated", "NOW()");

        $characters
            = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$%#&-/!()[]';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $password = $randomString;

        $item->password = password_hash($password, PASSWORD_BCRYPT);

        $item->save();

        // send email
        $message = '<p>Hi '.$this->post["firstname"].'</p>
        <p>You have been created as a tutor for New Skills Academy. As a tutor, you are able to manage the students assigned to you and their progress.</p>
        
        <p>You can sign in with your email, and the following password:</p>
        
        <p><strong>Password:</strong> '.$password.'</p>';


        $message .= $this->renderHtmlEmailButton("Sign In",
            SITE_URL);

        $this->sendEmail($this->post["email"], $message,
            "Your tutor login for ".SITE_NAME);

        $this->redirectJS(SITE_URL.'blume/ncfe/tutors');

    }

    public function createIqa() {

        $item = ORM::For_table("accounts")->create();

        $item->isIQA = "1";
        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->set_expr("whenCreated", "NOW()");
        $item->set_expr("whenUpdated", "NOW()");

        $characters
            = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$%#&-/!()[]';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $password = $randomString;

        $item->password = password_hash($password, PASSWORD_BCRYPT);

        $item->save();

        // send email
        $message = '<p>Hi '.$this->post["firstname"].'</p>
        <p>You have been created as an IQA user for New Skills Academy.</p>
        
        <p>You can sign in with your email, and the following password:</p>
        
        <p><strong>Password:</strong> '.$password.'</p>';


        $message .= $this->renderHtmlEmailButton("Sign In",
            SITE_URL);

        $this->sendEmail($this->post["email"], $message,
            "Your IQA login for ".SITE_NAME);

        $this->redirectJS(SITE_URL.'blume/ncfe/iqa');

    }

    public function editTutor() {

        $item = ORM::For_table("accounts")->find_one($this->get["id"]);

        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->set_expr("whenUpdated", "NOW()");

        if($this->post["password"] != "") {
            $item->password = password_hash($this->post["password"], PASSWORD_BCRYPT);
        }

        $item->save();

        $this->setAlertSuccess("Tutor account successfully updated");

    }

    public function editIqa() {

        $item = ORM::For_table("accounts")->find_one($this->get["id"]);

        $item->firstname = $this->post["firstname"];
        $item->lastname = $this->post["lastname"];
        $item->email = $this->post["email"];
        $item->set_expr("whenUpdated", "NOW()");

        if($this->post["password"] != "") {
            $item->password = password_hash($this->post["password"], PASSWORD_BCRYPT);
        }

        $item->save();

        $this->setAlertSuccess("IQA account successfully updated");

    }

    public function uploadSupportingDoc() {

        $item = ORM::for_table("courseDocuments")->create();

        $item->set_expr("whenAdded", "NOW()");
        $item->title = $this->post["title"];
        $item->courseID = $this->post["id"];
        $item->audience = $this->post["audience"];

        if ($this->checkFileUploadSelected() == true) {
            $sizes = [];
            $model = [
                'type' => 'courseController',
                'id'   => $this->post["id"]
            ];

            $media = $this->medias->uploadFile($sizes, $model, 'uploaded_file', 'supporting_doc', true);

            $item->mediaID = $media->id;

            $item->save();

            $this->blumeNew->recordLog('uploaded a supporting document to a course');

        }

        $this->setAlertSuccess("Supporting document successfully uploaded");

        // reload tables
        ?>
        <script>
            jQuery("#supportingDocsAjax").load('<?= SITE_URL ?>ajax?c=blumeNcfe&a=render-supporting-docs&id=<?= $this->post["id"] ?>');
        </script>
        <?php


    }

    public function renderSupportingDocs() {

        ?>
        <table class="table">
            <thead>
            <tr>
                <th>Document</th>
                <th>Audience</th>
                <th>When Added</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $items = ORM::for_table("courseDocuments")->where("courseID", $this->get["id"])->order_by_desc("id")->find_many();

            foreach($items as $item) {

                $media = ORM::for_table("media")->find_one($item->mediaID);

                ?>
                <tr id="supportingDoc<?= $item->id ?>">
                    <td>
                        <?= $item->title ?>
                    </td>
                    <td>
                        <?php
                        if($item->audience == "s") {
                            ?>
                            Student
                            <?php
                        } else {
                            ?>
                            Tutor
                            <?php
                        }
                        ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($item->whenAdded)) ?></td>
                    <td>

                        <label class="label label-danger" onclick="deleteSupportingDoc(<?= $item->id ?>);">
                            <i class="fa fa-times"></i>
                        </label>

                    </td>
                </tr>
                <?php

            }
            ?>
            </tbody>
        </table>

        <div id="deleteSupportingDoc"></div>
        <script>
            function deleteSupportingDoc(id) {
                Swal.fire({
                    title: 'Are you sure want to delete?',
                    //showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#f5393d',
                    //denyButtonText: `Don't save`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        jQuery("#supportingDoc"+id).remove();

                        jQuery("#deleteSupportingDoc").load('<?= SITE_URL ?>ajax?c=blumeNcfe&a=delete-supporting-doc&id='+id);
                    }
                });

            }
        </script>
        <?php

    }

    public function deleteSupportingDoc() {

        $item = ORM::for_table("courseDocuments")->find_one($this->get["id"]);
        $this->medias->deleteMediaByID( $item->mediaID);
        $item->delete();

    }

    public function deleteTutor() {

        $item = ORM::for_table("accounts")->where("isTutor", "1")->find_one($this->get["id"]);

        $item->delete();

    }

    public function deleteIqa() {

        $item = ORM::for_table("accounts")->where("isIQA", "1")->find_one($this->get["id"]);

        $item->delete();

    }

    public function reassignStudents() {

        $students = ORM::for_table("accounts")->where("tutorID", $this->get["id"])->find_many();

        foreach($students as $student) {

            $update = ORM::for_table("accounts")->find_one($student->id);

            $update->tutorID = $this->post["assignID"];

            $update->save();

        }

        $this->redirectJS(SITE_URL.'blume/ncfe/tutors');

    }

    public function manualCertificateUpload() {

        $item = ORM::for_table("coursesAssigned")->find_one($this->get["id"]);


        $postName = "file";
        $allow = array( 'pdf');
        $fileName = $_FILES[$postName]["name"];
        $fileTmpLoc = $_FILES[$postName]["tmp_name"];
        $fileType = $_FILES[$postName]["type"];
        $fileSize = $_FILES[$postName]["size"];
        $fileErrorMsg = $_FILES[$postName]["error"];
        $kaboom = explode(".", $fileName);
        $end = strtolower( end( $kaboom ) );

        if($fileSize > 10485760) {
            echo '<div class="alert alert-danger text-center" role="alert">Your image must be under 10MB.</div>';
            exit;
        } else if (!in_array($end,$allow) ) {
            echo '<div class="alert alert-danger text-center" role="alert">Please select a suitable PDF file.</div>';
            exit;
        } else if ($fileErrorMsg == 1) {
            echo '<div class="alert alert-danger text-center" role="alert">An unknown error occurred.</div>';
            exit;
        }

        $cdnDir = 'certificates';

        $fileName = $fileName.'-'.md5(time().rand(3333,9999)).'.'.$end;
        $moveResult = move_uploaded_file($fileTmpLoc, TO_PATH_CDN.$cdnDir.'/'.$fileName);
        if ($moveResult != true) {
            echo '<div class="alert alert-danger text-center" role="alert">We could not complete the upload of your PDF. Please contact support.</div>';
            exit;
        }

        $item->certFile = $fileName;

        // send email to student
        $account = ORM::for_table("accounts")->find_one($item->accountID);
        $course = ORM::for_table("courses")->select("title")->find_one($item->courseID);
        $message = '<p>Hello '.$account->firstname.',</p>
        
        <p>We have just uploaded a certificate for the following course you have recently completed: '.$course->title.'.</p>
     
        <p>To view this certificate, please sign into your account.</p>';

        // send email
        $this->sendEmail($account->email, $message, "We have just uploaded a certificate to your New Skills Academy account");


        $item->save();

        $this->setAlertSuccess("Certificate was successfully uploaded and the student was notified.");

    }

    public function actionMessage() {

        $messageItem = ORM::for_table("messages")->find_one($this->get["id"]);

        $messageItem->csApproved = "1";

        $student = ORM::for_table("accounts")->find_one($messageItem->userID);
        $tutor = ORM::for_table("accounts")->find_one($messageItem->recipientID);

        // send email to tutor
        $message = '<p>Hello '.$tutor->firstname.',</p>
        
        <p>'.$student->firstname.' '.$student->lastname.' has sent you the following message:</p>
        
        <p><em>"'.$messageItem->message.'"</em></p>
     
        <p>You can reply to this by signing into your account.</p>';

        // send email
        $this->sendEmail($tutor->email, $message, "You have a new message from ".$student->firstname." ".$student->lastname." on ".SITE_NAME);

        $messageItem->save();

        $this->setAlertSuccess("Thank you, this message was successfully sent on to the tutor");


    }

    public function deleteMessage() {

        $message = ORM::for_table("messages")->find_one($this->get["id"]);
        $message->delete();

    }

}
