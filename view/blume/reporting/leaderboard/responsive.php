<?php
// used to provide general reporting for subscriptions
$this->setControllers(array("blumeNew"));

$metaTitle = "Leaderboards";



include BASE_PATH . 'blume.header.base.php';
?>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        div.dt-buttons{
            float: right;
            margin-left: 20px;
        }
    </style>
    <!-- -------------- Content -------------- -->
    <section id="content" class="table-layout animated fadeIn">


        <!-- -------------- /Column Left -------------- -->

        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">


            <!-- -------------- Data Filter -------------- -->
            <div class="panel" id="spy2">
                <div class="panel-heading">
                    <?php
                    if($this->get["show"] == "last") {
                        ?>
                        <a href="<?= SITE_URL ?>blume/reporting/leaderboard" class="btn btn-system pull-right">
                            Show Current Month
                        </a>
                        <?php

                    } else {
                        ?>
                        <a href="<?= SITE_URL ?>blume/reporting/leaderboard?show=last" class="btn btn-system pull-right">
                            Show Last Month
                        </a>
                        <?php
                    }
                    ?>
                    <span class="panel-title">Leaderboard</span>
                    <p>Used to see top users on the rewards leaderboard for the past or current month.</p>


                </div>
                


                <div class="panel-body pn">
                    <div class="row mb20">
                        <div class="col-12">
                            </div>
                    </div>
                    
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="">
                                <table style="max-width: 100%;" class="table datatable table-responsive">
                                    <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Points</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $data = $this->blumeNew->getLeaderBoard();

                                    foreach($data as $item) {

                                        $account = ORM::for_table("accounts")->select("email")->find_one($item["userID"]);
                                        ?>
                                        <tr>
                                            <td><?= $item["userID"] ?></td>
                                            <td><?= $item["firstname"].' '.$item["lastname"] ?></td>
                                            <td><?= $account->email ?></td>
                                            <td><?= $item["total"] ?></td>
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


            <!-- -------------- DEMO Break -------------- -->
            <div class="mv40"></div>


        </div>
        <!-- -------------- /Column Center -------------- -->

    </section>
    <!-- -------------- /Content -------------- -->

<?php include BASE_PATH . 'blume.footer.base.php'; ?>