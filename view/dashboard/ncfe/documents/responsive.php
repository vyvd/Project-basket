<?php
$this->setControllers(array("accountDocument", "accountAssignment"));

$css = array("dashboard.css");
$pageTitle = "Documents";
include BASE_PATH . 'account-ncfe.header.php';
$this->setControllers(array("accountDocument"));
$supportingDocuments = $this->accountDocument->getSupportingDocuments();
?>

    <section class="page-title with-nav">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
            <ul class="nav navbar-nav inner-nav nav-tabs">
                <li class="nav-item link">
                    <a class="nav-link active show" href="#my" data-toggle="tab">My Documents</a>
                </li>
                <li class="nav-item link ">
                    <a class="nav-link" href="#supporting" data-toggle="tab">Supporting Documents</a>
                </li>
            </ul>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            <div class="row">
                <div class="col-12 regular-full">
                    <div class="tab-content container" style="padding:0;">

                        <div id="my" class="tab-pane active show">
                            <div class="row">
                                <div class="col-12 col-md-12 notification">

                                    <?php
                                    $assignments = ORM::For_table("accountAssignments")->where("accountID", CUR_ID_FRONT)->find_many();

                                    ?>
                                    <div class="row">
                                        <?php
                                        $count = 0;
                                        foreach($assignments as $assignment) {

                                            $files = $this->accountAssignment->getUserFilesByAssignmentID($assignment->id);

                                            foreach($files as $file) {

                                                $count ++;

                                                ?>
                                                <div class="col-12 col-md-6">
                                                    <div class="cardSmallRounded">
                                                        <a href="<?php echo AWSService::getFromS3('assignments/'.CUR_ID_FRONT.'/'.$assignment->moduleID.'/'.$file->fileName); ?>" target="_blank">
                                                            <?= $file->fileName ?? $file->title ?>
                                                            <i class="fad fa-file"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php

                                            }

                                        }
                                        ?>
                                    </div>
                                    <?php

                                    if($count == 0) {

                                        ?>
                                        <div class="white-rounded" style="padding:20px;">
                                            <p class="text-center" style="margin-top:10px;">
                                                You have not yet uploaded any documents. When you upload documents to your courses, or directly here, they will appear here.
                                            </p>
                                        </div>
                                        <?php

                                    }
                                    ?>

                                </div>
                            </div>
                        </div>

                        <div id="supporting" class="tab-pane">
                            <div class="row">
                                <?php
                                    if(count($supportingDocuments) >= 1) {
                                        foreach ($supportingDocuments as $document) {
                                            $media = ORM::for_table("media")->find_one($document->mediaID);
                                ?>
                                            <div class="col-12 col-md-6">
                                                <div class="cardSmallRounded">
                                                    <a href="<?= $media->url ?>" target="_blank">
                                                        <?= $document->title ?>
                                                        <i class="fad fa-file-pdf"></i>
                                                    </a>
                                                </div>
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
        </div>
    </section>



<?php include BASE_PATH . 'account.footer.php';?>