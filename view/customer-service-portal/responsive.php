<?php
$css = array("staff-training.css", "customer-service.css");
$pageTitle = "Customer Service Portal";

include BASE_PATH . 'header.php';
$this->setControllers(array("course"));
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css">
<style>
    #navBar {
        display:none !important;
    }
</style>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-center">Customer Service Portal</h1>
                    </div>
                </div>
            </div>
        </section>

        <br />
        <br />

        <!--Page Content-->
        <section class="staff-training">
            <div class="container wider-container">

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

                            <div class="panel-menu">
                                <form name="searchResults">
                                    <input name="search" type="text" class="form-control"
                                           placeholder="Search by name, email, order ID or certificate number (then hit enter)...">
                                    <br />
                                    <input type="submit" value="Search" class="btn btn-primary extra-radius" />
                                </form>
                            </div>
                            <br />
                            <br />
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

                                function loadSingleUser(id) {

                                    $("#searchResults").html('<iframe src="<?= SITE_URL ?>blume/accounts/view?id='+id+'&iframe=true" style="width:100%;height:1200px;border:0px;margin:0;"></iframe>');

                                }
                            </script>


                        </div>
                        <div id="tab2" class="tab-pane">

                            <form name="redeem">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Voucher Code" name="code" required>
                                </div>
                                <div class="form-group voucherCourses" style="display: none;">
                                    <select style="width: 100%; " class="form-control select2" name="courseID">
                                        <option value="">Select Course</option>
                                        <?php
                                            $rcourses = $this->course->getAllCourses('nameAsc');
                                            foreach ($rcourses as $rcourse){
                                        ?>
                                                <option value="<?= $rcourse->id?>"><?= $rcourse->title?></option>
                                        <?php
                                            }

                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="First Name" name="firstname" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Last Name" name="lastname" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" placeholder="Email Address" name="email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Choose a Password" name="password">
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Redeem Voucher" class="btn btn-primary extra-radius" />
                                </div>
                                <input type="hidden" name="admin" value="true" />
                            </form>
                            <?php
                            //$this->renderFormAjax("redeem", "redeem-voucher", "redeem");
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
        </section>


    </main>
    <!-- Main Content End -->

<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>

<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="height:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel" style="display:block;width:100%;">Add User</h4>
            </div>
            <form name="addNewItem" id="addNewAccount1">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Which course do you want to enroll the new user onto?</label>
                        <select class="form-control" name="courseID">
                            <option value="">No Course</option>
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
                        <input type="text" name="firstname" class="form-control csReset" />
                    </div>
                    <div class="form-group">
                        <label>Lastname</label>
                        <input type="text" name="lastname" class="form-control csReset" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control csReset" />
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
    $('.select2').select2();
    jQuery("form[name='redeem']").submit(function(e) {
        e.preventDefault();

        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=redeem&a=redeemVoucherCS",
            type: "POST",
            data: formData,
            async: true,
            dataType:"JSON",
            success: function (response) {
                if(response.error === true){
                    toastr.options.positionClass = "toast-bottom-left";
                    toastr.options.closeDuration = 1000;
                    toastr.options.timeOut = 5000;
                    toastr.warning(response.message, 'Oops');

                    if(response.showCourses === true){
                        $(".voucherCourses").css('display', 'block');
                    }
                }else if(response.success === true){
                    toastr.options.positionClass = "toast-bottom-left";
                    toastr.options.closeDuration = 1000;
                    toastr.options.timeOut = 5000;
                    toastr.success(response.message, 'Success');
                    $(".csReset").val('')
                }

                console.log(response);
                return false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });

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

<?php include BASE_PATH . 'footer.php';?>