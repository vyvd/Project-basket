<?php

$metaTitle = "Manage Account";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">

        <form name="newAdmin">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs"> Change Password</span>
            </div>
            <div class="panel-body pn">


                <div class="row">
                    <div class="col-xs-4">
                        <div class="section mb10 allcp-form theme-primary">
                            <label for="name21" class="field prepend-icon">
                                <input type="password" name="password" id="name21" class="event-name gui-input br-light light" placeholder="New Password" />
                                <label for="name21" class="field-icon">
                                    <i class="fa fa-lock"></i>
                                </label>
                            </label>
                        </div>
                    </div>

                    <div class="col-xs-4">
                        <div class="section mb10 allcp-form theme-primary">
                            <label for="name21" class="field prepend-icon">
                                <input type="password" name="passwordConfirm" id="name21" class="event-name gui-input br-light light" placeholder="Confirm New Password" />
                                <label for="name21" class="field-icon">
                                    <i class="fa fa-lock"></i>
                                </label>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <input type="submit" class="btn btn-info btn-block" value="Update" />
                    </div>
                </div>



                <style>
                    .checkbox {
                        margin-left:50px;
                    }
                </style>


                <div id="return_status"></div>

            </div>
        </div>
        </form>

        <script type="text/javascript">
            jQuery("form[name='newAdmin']").submit(function(e) {

                var formData = new FormData($(this)[0]);
                e.preventDefault();

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=change-password",
                    type: "POST",
                    data: formData,
                    async: false,
                    success: function (msg) {
                        jQuery('#return_status').append(msg);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });

            });
        </script>
        


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
