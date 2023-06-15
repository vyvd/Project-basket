<?php
$this->setControllers(array("account", "tutor", "courseModule"));

$this->account->restrictUserAccessTo("IQA");

$css = array("dashboard.css");
$pageTitle = "Tutors";
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
                                    <th>Email</th>
                                    <th>Students</th>
                                    <th>Outstanding Assignments</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $tutors = ORM::for_table("accounts")
                                    ->where('isTutor', '1')
                                    ->order_by_desc('id')
                                    ->find_many();

                                foreach($tutors as $account) {

                                    $lastSignIn = 'n/a';

                                    $log = ORM::for_table("accountSignInLogs")->where("accountID", $account->id)->order_by_desc("id")->find_one();

                                    if($log->id != "") {
                                        $lastSignIn = date('d/m/Y', strtotime($log->dateTime));
                                    }

                                    ?>
                                    <tr>
                                        <td><?= $account->firstname.' '.$account->lastname ?></td>
                                        <td><?= $account->email ?></td>
                                        <td><?= ORM::for_table("accounts")->where("tutorID", $account->id)->count() ?></td>
                                        <td>-</td>
                                        <td><?= $lastSignIn ?></td>
                                        <td>
                                            <a href="<?= SITE_URL ?>ajax?c=tutor&a=access-user-account&id=<?= $account->id ?>" class="btn btn-primary" style="width: 60px;font-size: 12px;margin-left:5px;">
                                                <i class="fas fa-repeat-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
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