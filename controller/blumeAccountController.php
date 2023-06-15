<?php
require_once(__DIR__ . '/accountController.php');

class blumeAccountController extends Controller
{

    /**
     * @var accountController
     */
    protected $accounts;

    public function __construct()
    {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->session = $_SESSION;
        $this->cookie = $_COOKIE;
        $this->accounts = new accountController();

        // if admin is not logged in then dont let them do anything
        if (CUR_ID == "") {
            header('Location: ' . SITE_URL . 'blume/login');
            exit;
        }

    }

    public function editAdminNotes() {

        $notes = ORM::for_table("accountAdminNotes")->where("accountID", $this->post["id"])->find_one();

        if($notes->accountID == "") {

            $notes = ORM::for_table("accountAdminNotes")->create();

            $notes->accountID = $this->post["id"];

        }

        $notes->notes = $this->post["adminNotes"];

        $notes->save();

        $this->setToastSuccess("Admin notes successfully saved");

    }

    public function addUserBalance() {

        $this->validateValues(array("amount", "description"));

        $this->accounts->addBalance($this->post["amount"], $this->post["description"], $this->post["accountID"]);

        $this->setAlertSuccess($this->post["amount"].' was successfully added to this account');

        $this->redirectJSDelay(SITE_URL.'blume/accounts/view?id='.$this->post["accountID"], '1500');

    }

    public function removeUserBalance() {

        $this->validateValues(array("amount", "description"));

        $this->accounts->removeBalance($this->post["amount"], $this->post["description"], $this->post["accountID"]);

        $this->setAlertSuccess($this->post["amount"].' was successfully removed from this account');

        $this->redirectJSDelay(SITE_URL.'blume/accounts/view?id='.$this->post["accountID"], '1500');

    }

}