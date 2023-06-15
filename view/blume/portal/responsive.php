<?php

$metaTitle = "Customer Service Portal";
include BASE_PATH . 'blume.header.base.php';
?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
<style>
    .select2.select2-container{
        width: 100% !important;
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
                <span class="panel-title">Customer Service Portal</span>
            </div>

            <br />

            <div class="tab-block">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab">Accounts</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="modal" data-target="#addUser">Register Account</a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab">Redeem Voucher</a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab">Resources</a>
                    </li>
                    <li>
                        <a href="#tab1" data-toggle="tab" onclick="showRewardsMessage()">Rewards</a>
                    </li>
                </ul>
                <div class="tab-content p30">

                    <div id="tab1" class="tab-pane active">

                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Courses</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Courses</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                                </tfoot>
                            </table>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $('.datatable').DataTable( {
                                        "processing": true,
                                        "serverSide": true,
                                        "ajax": {
                                            "type" : "GET",
                                            "url" : "<?= SITE_URL ?>blume/datatables/accounts",
                                            "dataSrc": function ( json ) {

                                                return json.data;

                                            }
                                        },
                                        "drawCallback": function( settings ) {

                                        }
                                    } );
                                } );


                            </script>
                        </div>

                    </div>
                    <div id="tab2" class="tab-pane">

                        <form name="redeem">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Voucher Code" name="code">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="First Name" name="firstname">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Last Name" name="lastname">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email Address" name="email">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Choose a Password" name="password">
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Redeem Voucher" class="btn btn-system" />
                            </div>
                            <input type="hidden" name="admin" value="true" />
                        </form>
                        <?php
                        $this->renderFormAjax("redeem", "redeem-voucher", "redeem");
                        ?>

                    </div>
                    <div id="tab3" class="tab-pane">

                        <form name="downloadResources" id="downloadResources">

                            <div class="form-group">
                                <label>Which course do you want to download the resources for?</label>
                                <select class="form-control" name="downloadID" id="downloadID" onchange="getDownloads();">
                                    <option value="">Select...</option>
                                    <?php
                                    foreach($this->getAllCoursesWithoutHidden() as $course) {
                                        ?>
                                        <option value="<?= $course->id ?>"><?= $course->title ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                        </form>

                        <div id="returnDownloads"></div>

                        <script>
                            function getDownloads() {

                                var id = $("#downloadID").val();

                                $.post("<?= SITE_URL ?>ajax?c=blumeNew&a=course-download-content",
                                    {
                                        id: id
                                    },
                                    function(data, status){

                                        $("#returnDownloads").html(data);

                                    });
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add User</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Which course do you want to enroll the new user onto?</label>
                        <select class="form-control" name="courseID">
                            <?php
                            foreach($this->getAllCoursesWithoutHidden() as $course) {
                                ?>
                                <option value="<?= $course->id ?>"><?= $course->title ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Firstname</label>
                        <input type="text" name="firstname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" name="lastname" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="passwordConfirm" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Voucher Code <small>Only enter if you want to redeem one on behalf of this new user</small></label>
                        <input type="text" name="voucherCode" class="form-control" />
                    </div>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=create-user-account",
            type: "POST",
            data: formData,
            async: true,
            success: function (msg) {
                jQuery('#returnStatusAddNew').html(msg);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    function showRewardsMessage() {

        toastr.options.positionClass = "toast-bottom-left";
        toastr.options.closeDuration = 1000;
        toastr.options.timeOut = 5000;
        toastr.error('Rewards can be added within a users account. Search them here first, then assign rewards from within their account.', 'Please note')

    }
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
