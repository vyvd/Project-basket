<?php
$course = $this->controller->getCourseEdit();
$metaTitle = "Order Modules";
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
                <span class="panel-title">Re-order modules for <?= $course->title ?></span>
                <a href="<?= SITE_URL ?>blume/courses/edit?id=<?= $course->id ?>" class="btn btn-warning pull-right">
                    Back to Course
                </a>
            </div>
            <div class="panel-body pn">
                <p>Simply drag to change the order in the list below, and the order will automatically save.</p>
                <hr />
                <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
                <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

                <ul id="sortslidepods">
                    <?php
                    $items = ORM::for_table("courseModules")->where("courseID", $course->id)->order_by_asc("ord")->find_many();
                    foreach($items as $item) {
                        echo '<li id="slide_'.$item->id.'" style="cursor:pointer;background:#eee;padding:5px;margin-bottom:5px;">'.$item->title.'</li>';
                    }
                    ?>

                </ul>

                <script type="text/javascript">
                    $( document ).ready(function() {
                        $("#sortslidepods").sortable({
                            update: function () {

                                var serial = $('#sortslidepods').sortable('serialize');
                                serial = serial.split('[').join('');
                                serial = serial.split(']').join('');
                                serial = serial.split('&').join('-');
                                serial = serial.split('slide=').join('');

                                console.log(serial);

                                $.ajax({
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=order-course-modules&serial="+serial,
                                    type: "post",
                                    data: serial,
                                    error: function () {
                                        alert("Oops! The order cannot be saved.");
                                    }
                                });
                            }

                        });
                    });
                </script>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>


<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
