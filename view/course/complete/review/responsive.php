<?php
$course = $this->controller->getCourseComplete();
$courseAssignedDetails = $this->controller->getCourseCompleteAssigned();

$pageTitle = "Rate Course";
include BASE_PATH . 'account.header.php';
?>

    <div class="container-fluid" id="learn-zone">
        <div class="container containerSmall">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="">

                        <div class="quiz-question">

                            <div class="quizPassed">

                                <?php
                                    if($course->isNCFE == '1'){
                                ?>
                                        <script type="text/javascript" src="https://form.jotform.com/jsform/212351943307350"></script>
                                <?php
                                    }else{
                                ?>
                                        <h2 class="quizResultTitle">Rate Your Course</h2>

                                        <p class="quizTextSmall">We'd love to hear what you thought of the <?= $course->title ?> course. Please leave your rating and thoughts below.</p>

                                        <form name="rating">
                                            <div class="rating">
                                                <label>
                                                    <input type="radio" name="rating" value="5" title="5 stars"> 5
                                                </label>
                                                <label>
                                                    <input type="radio" name="rating" value="4" title="4 stars"> 4
                                                </label>
                                                <label>
                                                    <input type="radio" name="rating" value="3" title="3 stars"> 3
                                                </label>
                                                <label>
                                                    <input type="radio" name="rating" value="2" title="2 stars"> 2
                                                </label>
                                                <label>
                                                    <input type="radio" name="rating" value="1" title="1 star"> 1
                                                </label>
                                            </div>
                                            <input type="hidden" name="course" value="<?= $course->id ?>" />
                                        </form>
                                    <?php
                                    $this->renderFormAjax("course", "rate-course", "rating");
                                    ?>
                                        <script>
                                            $('.rating input').change(function () {
                                                var $radio = $(this);
                                                $('.rating .selected').removeClass('selected');
                                                $radio.closest('label').addClass('selected');
                                                $radio.closest('form').submit();
                                            });
                                        </script>
                                <?php
                                    }
                                ?>


                                <p class="quizTextSmall" style="margin-top:22px;">
                                    <a href="<?= SITE_URL ?>dashboard">Skip</a>
                                </p>

                            </div>

                        </div>


                        <?php
                        $studyGroupUrl = $this->getSetting("study_group_url");

                        if($studyGroupUrl != "") {
                            ?>
                            <div class="col-12 regular-full">
                                <a href="<?= $studyGroupUrl ?>" target="_blank">
                                    <img src="<?= SITE_URL ?>assets/images/study-group-banner.png" width="100%" />
                                </a>
                            </div>
                            <?php
                        }
                        ?>



                    </div>
                </div>
            </div>

        </div>
    </div>

<?php include BASE_PATH . 'account.footer.php';?>