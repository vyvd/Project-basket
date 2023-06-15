<?php
$pageTitle = "Course Notes";
include BASE_PATH . 'header.php';
include BASE_PATH . 'account-top.php';

?>

    <div class="container-fluid" id="checkout">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div id="checkout-block">
                        <div class="row">
                            <div class="col-lg-3 col-12" id="account-menu">
                                <?php
                                include BASE_PATH . 'account-left.php';
                                ?>
                            </div>
                            <div class="col-lg-9 col-12">

                                <div class="account-block">
                                    <h3 class="account-heading">Course Notes</h3>
                                    <div class="row">
                                        <div class="col-12">
                                            <?php
                                            $notes = $this->controller->getUserNotes();

                                            foreach($notes as $note) {

                                                $course = $this->controller->getCourseByID($note->courseID);
                                                $module = $this->controller->getModuleByID($note->moduleID);

                                                ?>
                                                <div class="course-note">
                                                    <h4><a href="<?= SITE_URL ?>module/<?= $module->slug ?>"><?= $course->title ?>: <?= $module->title ?></a></h4>
                                                    <div class="notes-icons">
                                                        <a href="#"><i class="fal fa-print" aria-hidden="true"></i></a>
                                                        <a href="#"><i class="fal fa-file-word" aria-hidden="true"></i></a>
                                                        <a href="<?= SITE_URL ?>ajax?c=course&a=delete-course-note&id=<?= $note->id ?>"><i class="fal fa-trash-alt" aria-hidden="true"></i></a>
                                                    </div>
                                                    <h5>Notes:</h5>
                                                    <p><?= $note->notes ?></p>
                                                </div>
                                                <?php


                                            }

                                            if(count($notes) == 0) {
                                                ?>
                                                <p class="text-center">
                                                    You've not yet made any notes.
                                                </p>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include BASE_PATH . 'learn-confidence.php';?>
<?php include BASE_PATH . 'success-stories.php';?>
<?php include BASE_PATH . 'validate-prospects.php';?>
<?php include BASE_PATH . 'award-winning.php';?>
<?php include BASE_PATH . 'footer.php';?>