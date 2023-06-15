<?php
$pageTitle = "Jobs";
include BASE_PATH . 'account.header.php';
?>
<section class="page-title">
    <div class="container">
        <h1><?= $pageTitle ?></h1>
    </div>
</section>

<section class="page-content">
    <div class="container" style="  margin: auto;width: 100%;border: 2px none green;padding: 10px;">
        <div class="row">
            <div class="col-12 regular-full popularCourseboxes">
                <div class="row">
                    <?php
                    $items = ORM::for_table("jobs")->where("jobState", "on")->order_by_desc("id")->find_many();
                    if (count($items) == 0) {
                    ?>
                    <!-- error message if there is no jobs -->
                        <div class="col-12 text-center noSavedCourses">
                            Looks like there are currently no Jobs available, try again later.
                        </div>
                    <?php
                    }                    
                    $currentTime =date('h:i A', time());
                    
                    foreach ($items as $item) {
                        
                        $endTime = $item->closingDate;
                        if((strtotime($currentTime) >= strtotime($endTime)) ){
                            $item->set(
                                array(
                                    'jobState'  => "off",
                                )
                            );
                            $item->save();
                        }

                       
                    ?>
                        <!-- start of repeat to display job cards for the board  -->
                        <div class="container">
                            <div class="col-md-16">
                                <div class="card">
                                    <div class="row">
                                        <div class="w-100 p-4 btn-light border-bottom border-top bg-white">
                                            <div class="row">
                                                <div class="col-12 col-sm-8 col-md-9">
                                                    <h5><a style="color: rgb(48, 127, 194);"><?= $item->jobTitle ?></a></h5>
                                                    <p>
                                                        <i class="fa fa-building pr-1"></i><?= $item->companyName ?>
                                                        <i class="fa fa-map-marker pr-1 pl-2"></i><?= $item->location ?><span class="d-block">
                                                            <i class="fa fa-clock"></i> <span class="text-success"><?= $item->closingDate ?></span>
                                                        </span>
                                                    </p>
                                                    <p>
                                                        <?= $item->jobDescription  ?></p>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-3">
                                                    <!-- onclick of the job button the time the user clicked the button willbe sent to the database and then taken to the application link -->
                                                    <a target="_blank"  onclick=" clickAmount(<?= $item->id ?> ,<?= $jobs->clickAmount ?>); clickJobs(<?= $item->id ?>, <?= CUR_ID_FRONT ?>);" href="<?= $item->applicationLink ?>" style="text-align: center;" class="btn-primary btn-view-job btn-lg btn-block">View job</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end of repeat -->
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include BASE_PATH . 'account.footer.php'; ?>
<!-- sends jobID and accountID to the database  -->
<script type="text/javascript">
        function clickAmount(jobID, clickAmount) {
            clickAmount++
        $.ajax({
            url: "<?= SITE_URL ?>ajax?c=jobs&a=click-Amount",
            type: "POST",
            data: {
                'jobID': jobID,
                'clickAmount': clickAmount,
            },
            success: function(response) {
                console.log(response);
            },

        });
    }

    function clickJobs(jobID, accountID) {

        $.ajax({
            url: "<?= SITE_URL ?>ajax?c=jobs&a=job-Clicks",
            type: "POST",
            data: {
                'jobID': jobID,
                'accountID': accountID,
            },
            success: function(response) {
                console.log(response);
            },

        });
    }


</script>