<?php
$this->setControllers(array("course", "account"));

$this->account->restrictUserAccessTo("NCFE");

$css = array("dashboard.css");
$pageTitle = "Thank you";
include BASE_PATH . 'account-ncfe.header.php';

$total = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("accountID", CUR_ID_FRONT)->count();
$active = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "0")->where("accountID", CUR_ID_FRONT)->count();
$completed = ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "1")->where("accountID", CUR_ID_FRONT)->count();
$messages = ORM::for_table('messagesQueue')->order_by_desc('whenSent')->limit(5)->find_many();
?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>
    <section class="page-content">
        <div class="container assessment-thankyou">
            <div class="row white-rounded p-4 mt-5">
                <div class="col-12 pt-5" style="text-align: center">
                    <i class="fas fa-check-circle"></i>
                    <h1 class="mt-3">Thank you</h1>
                    <h4 class="mt-4">Your work has been submitted to your tutor.</h4>
                    <p>Please allow up to 7 days for them to mark and return your assessment(s)</p>
                    <p>If you need any assistance in the meantime please <a href="#">contact our student support assistants</a></p>
                </div>
            </div>
        </div>
    </section>


<?php include BASE_PATH . 'account.footer.php';?>