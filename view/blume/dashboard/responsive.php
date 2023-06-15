<?php

$metaTitle = "Dashboard";
include BASE_PATH . 'blume.header.base.php';
?>



    <!-- -------------- Content -------------- -->
    <section id="content" class="table-layout animated fadeIn">

        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">

            <div class="panel" id="spy2">
                <div class="panel-heading">
                    <span class="panel-title">Search...</span>
                </div>
                <div class="panel-menu">
                    <form name="searchResults">
                        <input name="search" type="text" class="form-control"
                               placeholder="Search by name, email, order ID or certificate number...">
                    </form>
                </div>
                <div id="searchResults"></div>

                <script type="text/javascript">
                    $("form[name='searchResults']").submit(function(e) {
                        var formData = new FormData($(this)[0]);
                        e.preventDefault();
                        $( "#searchResults" ).empty();

                        $.ajax({
                            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=dashboard-search",
                            type: "POST",
                            data: formData,
                            async: true,
                            success: function (msg) {
                                $('#searchResults').append(msg);
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    });

                </script>


            </div>



            <!-- -------------- Quick Links -------------- -->
            <div class="row">

                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart2.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Users</h6>

                                    <h2 class="fs50 mt5 mbn"><?= ORM::for_table("accounts")->count(); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Total Orders</h6>
                                    <h2 class="fs50 mt5 mbn"><?= number_format(ORM::for_table("orders")->where("status", "completed")->count()); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $orders = ORM::for_table("orders")
                    ->where("status", "completed")
                    ->where_gt("whenCreated", date('Y-m-d', strtotime('-7 day')).' 00:00:00')
                    ->count();
                ?>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Orders (Last 7 Days)</h6>
                                    <h2 class="fs50 mt5 mbn"><?= $orders ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Course Enrolments</h6>
                                    <h2 class="fs50 mt5 mbn"><?= ORM::for_table("coursesAssigned")->count(); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Earnings (Today)</h6>
                                    <?php
                                    $orders = ORM::for_table("orders")->where("status", "completed")->where_like("whenCreated", "%".date('Y-m-d')."%")->find_many();

                                    $total = 0;

                                    foreach($orders as $order) {
                                        $total = $total+$order->totalGBP;
                                    }

                                    ?>
                                    <h2 class="fs50 mt5 mbn" style="font-size: 28px !important;margin-top: 41px !important;">£<?= number_format($total, 2) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Earnings (Yesterday)</h6>
                                    <?php
                                    $orders = ORM::for_table("orders")->where("status", "completed")->where_like("whenCreated", "%".date('Y-m-d', strtotime('-1 days'))."%")->find_many();

                                    $total = 0;

                                    foreach($orders as $order) {
                                        $total = $total+$order->totalGBP;
                                    }

                                    ?>
                                    <h2 class="fs50 mt5 mbn" style="font-size: 28px !important;margin-top: 41px !important;">£<?= number_format($total, 2) ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Learning Time (Today)</h6>
                                    <?php
                                    $hours = ORM::for_table('person')->raw_query('SELECT SUM(`seconds`) as seconds FROM `courseTimeProgress` WHERE date = :date', array('date' => date('Y-m-d')))->find_many();

                                    $seconds = 0;

                                    foreach($hours as $hour) {
                                        $seconds = $hour->seconds;
                                    }


                                    ?>
                                    <h2 class="fs50 mt5 mbn" style="font-size: 28px !important;margin-top: 41px !important;"><?= number_format($seconds/3600, 2) ?> hours</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <div class="row pv10">
                                <div class="col-xs-5 ph10"><img src="<?= SITE_URL ?>assets/blume/img/pages/clipart0.png"
                                                                class="img-responsive mauto" alt=""/></div>
                                <div class="col-xs-7 pl5">
                                    <h6 class="text-muted">Learning Time (Yesterday)</h6>
                                    <?php
                                    $hours = ORM::for_table('person')->raw_query('SELECT SUM(`seconds`) as seconds FROM `courseTimeProgress` WHERE date = :date', array('date' => date('Y-m-d', strtotime(' -1 day'))))->find_many();

                                    $seconds = 0;

                                    foreach($hours as $hour) {
                                        $seconds = $hour->seconds;
                                    }


                                    ?>
                                    <h2 class="fs50 mt5 mbn" style="font-size: 28px !important;margin-top: 41px !important;"><?= number_format($seconds/3600, 2) ?> hours</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <h4>Today's Most Engaging Courses</h4>
                            <?php
                            $hours = ORM::for_table('person')->raw_query('SELECT SUM(`seconds`) as total_seconds, courseID FROM `courseTimeProgress` WHERE date = :date GROUP BY courseID ORDER BY SUM(`seconds`) DESC LIMIT 50', array('date' => date('Y-m-d')))->find_many();

                            foreach($hours as $hour) {

                                $course = ORM::for_table("courses")->find_one($hour->courseID);

                                ?>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <strong><?= $course->title ?></strong>
                                    </div>
                                    <div class="col-xs-4 text-right">
                                        <label class="label label-system"><?= number_format($hour->total_seconds/3600, 2) ?> hours</label>
                                    </div>
                                </div>
                                <?php

                            }

                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile">
                        <div class="panel-body">
                            <h4>Yesterday's Most Engaging Courses</h4>
                            <?php
                            $hours = ORM::for_table('person')->raw_query('SELECT SUM(`seconds`) as total_seconds, courseID FROM `courseTimeProgress` WHERE date = :date GROUP BY courseID ORDER BY SUM(`seconds`) DESC LIMIT 50', array('date' => date('Y-m-d', strtotime('-1 day'))))->find_many();

                            foreach($hours as $hour) {

                                $course = ORM::for_table("courses")->find_one($hour->courseID);

                                ?>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <strong><?= $course->title ?></strong>
                                    </div>
                                    <div class="col-xs-4 text-right">
                                        <label class="label label-system"><?= number_format($hour->total_seconds/3600, 2) ?> hours</label>
                                    </div>
                                </div>
                                <?php

                            }




                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- -------------- AllCP Info -------------- -->




        </div>
        <!-- -------------- /Column Center -------------- -->

        <!-- -------------- Column Right -------------- -->

        <!-- -------------- /Column Right -------------- -->

    </section>
    <!-- -------------- /Content -------------- -->





<?php include BASE_PATH . 'blume.footer.base.php'; ?>