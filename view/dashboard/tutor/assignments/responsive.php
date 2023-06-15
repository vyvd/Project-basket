<?php
$this->setControllers(array("account", "tutor", "courseModule"));

$this->account->restrictUserAccessTo("tutor");

$css = array("dashboard.css");
$pageTitle = "Assignments";
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
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Lesson</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $students = $this->tutor->getStudents(99);
                                foreach($students as $account) {
                                    $items = ORM::For_table("accountAssignments")->where("accountID", $account->id)->find_many();

                                    foreach($items as $item) {

                                        $course = ORM::for_table("courses")->find_one($item->courseID);
                                        $lesson = ORM::for_table("courseModules")->find_one($item->moduleID);


                                            $module = ORM::for_table("courseModules")->find_one($course->currentModule);

                                            $status = '<label class="label label-warning">In Progress</label>';

                                        if($item->status == "1") {
                                            $status = '<label class="label label-info">Pending Feedback</label>';
                                        } else if($item->status == "3") {
                                            $status = '<label class="label label-danger">Refer</label>';
                                        } else if($item->status == "4") {
                                            $status = '<label class="label label-success">Completed</label>';
                                        }

                                            ?>
                                            <tr>
                                                <td><?= $account->firstname.' '.$account->lastname ?></td>
                                                <td><?= $course->title ?></td>
                                                <td><?= $lesson->title ?></td>
                                                <td><?= $status ?></td>
                                                <td><?= date('d/m/Y', strtotime($item->created_at)) ?></td>
                                                <td>
                                                    <a href="<?= SITE_URL ?>dashboard/tutor/student/<?= $account->id ?>" class="label label-system" style="width: 140px;">
                                                        See More
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php


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