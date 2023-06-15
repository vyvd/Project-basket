<?php

$metaTitle = "Reporting - Course Categories";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title">Reporting: Course Categories</span>

            </div>
            <div class="panel-menu">
                <input id="fooFilter" type="text" class="form-control"
                       placeholder="Search...">
            </div>
            <div class="panel-body pn">
                <div class="table-responsive">
                    <table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>Enrollments</th>
                            <th>Unstarted</th>
                            <th>Incomplete</th>
                            <th>Completed</th>
                            <th>Total Spend</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("courseCategories")->order_by_asc("title")->find_many();

                        foreach($items as $item) {

                            $courseArray = array();

                            $courses = ORM::for_table("courses")->where("category", $item->id)->find_many();

                            foreach($courses as $course) {
                                array_push($courseArray, $course->id);
                            }

                            // where in
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->title ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where_in("courseID", $courseArray)->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where_in("courseID", $courseArray)->where_null("currentModule")->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where_in("courseID", $courseArray)->where_not_null("currentModule")->where("completed", "0")->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where_in("courseID", $courseArray)->where("completed", "1")->count() ?>
                                </td>
                                <td>
                                    <?php
                                    $orderItems = ORM::For_table("orderItems")->where_in("courseID", $courseArray)->find_many();

                                    $total = 0;
                                    foreach($orderItems as $orderItem) {

                                        $total = $total+$orderItem->price;

                                    }
                                    ?>
                                    £<?= number_format($total, 2) ?>
                                </td>
                            </tr>

                            <?php
                        }
                        ?>
                        </tbody>
                        <tfoot class="footer-menu">
                        <tr>
                            <td colspan="7">
                                <nav class="text-right">
                                    <ul class="pagination hide-if-no-paging"></ul>
                                </nav>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
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
