<?php

$metaTitle = "Reporting - Courses";
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
                <span class="panel-title">Reporting: Courses</span>

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
                            <th>Course</th>
                            <th>Enrollments</th>
                            <th>Unstarted</th>
                            <th>Incomplete</th>
                            <th>Completed</th>
                            <th>Total Spend</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("courses")->order_by_asc("title")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->title ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where("courseID", $item->id)->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where("courseID", $item->id)->where_null("currentModule")->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where("courseID", $item->id)->where_not_null("currentModule")->where("completed", "0")->count() ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("coursesAssigned")->where("courseID", $item->id)->where("completed", "1")->count() ?>
                                </td>
                                <td>
                                    <?php
                                    $orderItems = ORM::For_table("orderItems")->where("courseID", $item->id)->find_many();

                                    $total = 0;
                                    foreach($orderItems as $orderItem) {

                                        $total = $total+$orderItem->price;

                                    }
                                    ?>
                                    £<?= number_format($total, 2) ?>
                                </td>
                                <td>
                                    <a href="<?= SITE_URL ?>blume/courses/edit?id=<?= $item->id ?>" class="label label-warning" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-edit"></i>
                                    </a>
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
