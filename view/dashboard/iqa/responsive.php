<?php
$this->setControllers(array("account"));

$this->account->restrictUserAccessTo("IQA");

$css = array("dashboard.css");
$pageTitle = "IQA Dashboard";
include BASE_PATH . 'account.header.php';

?>
    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <style>
        .circle-outer a {
            color:#000;
        }
        .circle-outer a:hover {
            color:#248CAB;
            text-decoration:none;
        }
    </style>

    <section class="page-content" id="dashboardElement">
        <div class="container">

            <div class="row align-items-center" style="margin-top:30px;">
                <div class="col-12 col-md-12 col-lg-4 no-padding">
                    <div class="white-rounded dash-details">
                        <h1 class="text-center"><?= ORM::for_table("accounts")->where_not_null("tutorID")->count(); ?><sup><i class="fas fa-arrow-up"></i></sup></h1>
                        <h3 class="text-center">Total Students</h3>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 no-padding">
                    <div class="white-rounded dash-details">
                        <h1 class="text-center">0</h1>
                        <h3 class="text-center">Pending Assignments</h3>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 no-padding">
                    <div class="white-rounded dash-details">
                        <h1 class="text-center">0</h1>
                        <h3 class="text-center">Completed Assignments</h3>
                    </div>
                </div>
            </div>


            <div class="white-rounded" style="padding:20px;margin-top:30px;">
                <h4>Tutors</h4>
                <table id="example" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach(ORM::for_table("accounts")->where("isTutor", "1")->find_many() as $account) {
                        ?>
                        <tr>
                            <td><?= $account->firstname.' '.$account->lastname ?></td>
                            <td><?= $account->email ?></td>
                            <td><?= ORM::for_Table("accounts")->where("tutorID", $account->id)->count() ?></td>
                            <td>
                                <a href="<?= SITE_URL ?>ajax?a=account&a=access-tutor-account" class="btn btn-primary" style="width: 140px;font-size: 12px;">
                                    See More
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <div class="white-rounded" style="padding:20px;margin-top:30px;">
                <h4>Pending Certificates</h4>

                <br />
                <br />
                <br />
                <p class="text-center"><em>There's currently no one awaiting a certificate. When someone has passed a course, they will show here so that their certificate can be uploaded...</em></p>
                <br />
                <br />
                <br />
            </div>

        </div>
    </section>

<?php include BASE_PATH . 'account.footer.php';?>