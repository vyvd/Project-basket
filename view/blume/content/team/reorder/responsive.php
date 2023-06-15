<?php
$metaTitle = "Order Team";
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
                <span class="panel-title">Re-order team members</span>
                <a href="<?= SITE_URL ?>blume/content/team" class="btn btn-warning pull-right">
                    Back to Team
                </a>
            </div>
            <div class="panel-body pn">
                <p>Simply drag to change the order in the list below, and the order will automatically save.</p>
                <hr />
                <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
                <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

                <ul id="sortslidepods">
                    <?php
                    $items = ORM::for_table("aboutTeam")->order_by_asc("ord")->find_many();
                    foreach($items as $item) {
                        echo '<li id="slide_'.$item->id.'" style="cursor:pointer;background:#eee;padding:5px;margin-bottom:5px;">'.$item->name.'</li>';
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
                                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=order-team&serial="+serial,
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
