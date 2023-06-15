<?php
$this->setControllers(array("account", "tutor", "courseModule"));

$this->account->restrictUserAccessTo("tutor");

$css = array("dashboard.css");
$pageTitle = "Tutor Dashboard";
include BASE_PATH . 'account.header.php';

?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <style>
        .circle-outer a {
            color: #000;
        }

        .circle-outer a:hover {
            color: #248CAB;
            text-decoration: none;
        }
    </style>

    <section class="page-content" id="dashboardElement">
        <div class="container">


            <div class="col-12 regular-full">
                <div class="row align-items-center">
                    <div class="col-12 col-md-12 col-lg-4 no-padding">
                        <div class="white-rounded dash-details">
                            <span style="display:none;">My Students</span>
                            <h1 class="text-center"><?= $this->tutor->getTotalStudents() ?>
                                <sup><i class="fas fa-arrow-up"></i></sup></h1>
                            <h3 class="text-center">My Students</h3>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4 no-padding">
                        <div class="white-rounded dash-details">
                            <h1 class="text-center"><?= $this->tutor->getPendingAssignmentsCount() ?></h1>
                            <h3 class="text-center">Pending Assignments</h3>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4 no-padding">
                        <div class="white-rounded dash-details">
                            <h1 class="text-center"><?= ORM::for_table("messages")->where("recipientID", CUR_ID_FRONT)->where("seen", "0")->count(); ?></h1>
                            <h3 class="text-center">Unread Messages</h3>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top:50px;">
                    <div class="col-12 col-md-12 col-lg-6">
                        <div class="tutorDashList">
                            <h4>Student List</h4>
                            <?php
                                $students = $this->tutor->getStudents(5);
                                if(count($students)){
                                    foreach ($students as $student) {
                            ?>
                                        <div class="allagmt">
                                            <h5>
                                                <a href="<?= SITE_URL . 'dashboard/tutor/student/' . $student->id ?>">
                                                    <?= $student->firstname. " ".$student->lastname ?>
                                                    <i class="fa fa-chevron-right float-right"></i>
                                                </a>
                                            </h5>
                                        </div>
                            <?php
                                    }
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
                        <div class="tutorDashList">
                            <h4>Submitted Assignments</h4>
                            <?php
                            $assignments = $this->tutor->getAssignments(false, 1);
                            if(count($assignments)){
                                foreach ($assignments as $assignment) {
                                    $module = $this->courseModule->getModuleByID($assignment->moduleID);
                                    $account = $this->account->getAccountByID($assignment->accountID);
                                    ?>
                                    <div class="allagmt">
                                        <h5>
                                            <a href="<?= SITE_URL . 'dashboard/tutor/student/' . $assignment->accountID.'?id='.$assignment->id.'#module'. $assignment->moduleID?>">
                                                <?= $module->title ?>
                                                <label class="status payment_pending"><?= $account->firstname. " ".$account->lastname ?></label>
                                            </a>

                                        </h5>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

<?php include BASE_PATH . 'account.footer.php'; ?>