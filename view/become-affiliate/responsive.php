<?php
$css = array("staff-training.css", "single-course.css");
$pageTitle = "Become An Affiliate";

$breadcrumb = array(
    "Become An Affiliate" => '',
);

include BASE_PATH . 'header.php';
?>
<style>
    .btn-link, .btn-link:hover, .btn-link:focus, .btn-link:active {
        color: #248cab;
    }
</style>
    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">Become an Affiliate</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">
                <div class="row">


                    <div class="col-12 col-md-12 col-lg-6 contact-details affiliate">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#info">Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#benefits">Benefits</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#faq">FAQ’s</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content contact-boxes">

                            <!-- Info pane -->
                            <div id="info" class="container tab-pane active">
                                <?php
                                $info = $this->getPage(8);
                                echo $info->contents;
                                ?>
                            </div>

                            <!-- benefits pane -->
                            <div id="benefits" class="container tab-pane fade">
                                <?php
                                $info = $this->getPage(9);
                                echo $info->contents;
                                ?>
                            </div>

                            <!-- Faq pane -->
                            <div id="faq" class="container tab-pane fade"><br>
                                <div id="accordion">
                                    <?php
                                    foreach(ORM::for_table("affiliateFaqs")->find_many() as $faq) {
                                        ?>
                                        <div class="card grey-bg-box">
                                            <div class="card-header" id="heading<?= $faq->id ?>">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#question<?= $faq->id ?>" aria-expanded="false" aria-controls="question<?= $faq->id ?>">
                                                        <?= $faq->question ?>
                                                    </button>
                                                </h5>
                                            </div>
                                            <div id="question<?= $faq->id ?>" class="collapse" aria-labelledby="heading<?= $faq->id ?>" data-parent="#accordion">
                                                <div class="card-body">
                                                    <?= $faq->answer ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-12 col-md-12 col-lg-6 staff-form contact affiliate">
                        <span class="title-tab">Apply to Join</span>

                        <h4>Complete the quick enquiry form below and one of our student advisers will be in touch shortly</h4>
                        <script type="text/javascript" src="https://form.jotformpro.com/jsform/70964368892978"></script>
                    </div>

                </div>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->


<?php include BASE_PATH . 'footer.php';?>