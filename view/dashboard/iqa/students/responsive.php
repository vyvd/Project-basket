<?php
$this->setControllers(array("account", "tutor", "courseModule"));

$this->account->restrictUserAccessTo("IQA");

$css = array("dashboard.css");
$pageTitle = "Students";
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


                <div class="row" style="margin-top:50px;">
                    <div class="col-12 col-md-12 col-lg-12">

                        <div class="white-rounded" style="padding:20px;">
                            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Current Lesson</th>
                                    <th>Started</th>
                                    <th>Assignments</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $students = ORM::for_table("accounts")
                                    ->where('isNCFE', '1')
                                    ->order_by_desc('id')
                                    ->find_many();

                                foreach($students as $account) {
                                    $courses = ORM::For_table("coursesAssigned")->where("accountID", $account->id)->find_many();

                                    foreach($courses as $course) {

                                        $courseData = ORM::for_table("courses")->select("id")->select("title")->select("isNCFE")->find_one($course->courseID);

                                        if($courseData->isNCFE == "1") {
                                            $module = ORM::for_table("courseModules")->find_one($course->currentModule);
                                            ?>
                                            <tr>
                                                <td><?= $account->firstname.' '.$account->lastname ?></td>
                                                <td><?= $courseData->title ?></td>
                                                <td><?= $module->title ?></td>
                                                <td><?= date('d/m/Y', strtotime($course->whenAssigned)) ?></td>
                                                <td><?= ORM::for_Table("accountAssignments")->where("accountID", $account->id)->where("courseID", $courseData->id)->count() ?></td>
                                                <td>
                                                    <a href="<?= SITE_URL ?>dashboard/tutor/student/<?= $account->id ?>" class="btn btn-primary" style="width: 60px;font-size: 12px;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= SITE_URL ?>ajax?c=tutor&a=access-user-account&id=<?= $account->id ?>" class="btn btn-primary" style="width: 60px;font-size: 12px;margin-left:5px;">
                                                        <i class="fas fa-repeat-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }

                                    }

                                    ?>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.js"></script>

    <script>
        $(document).ready( function () {
            $('#datatable').DataTable();
        } );
    </script>


<?php include BASE_PATH . 'account.footer.php'; ?>