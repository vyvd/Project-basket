<?php

$metaTitle = "Accounts";
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

                    <?php
                    if ($this->get["deleted"] == "true") {
                        $this->setAlertDanger("The account was successfully deleted. This action cannot be undone.");
                    }
                    ?>
                    <button data-toggle="modal" data-target="#exportBusinessEmails"
                            class="btn btn-dark pull-right ml10">
                        <i class="fa fa-download"></i> Export Business Emails
                    </button>
                    <button data-toggle="modal" data-target="#addNewImport" class=" btn btn-dark pull-right ml10"><i
                                class="fa fa-download"></i> Import User By OldID
                    </button>
                    <button data-type="multiple" data-url="<?= SITE_URL ?>ajax?c=import&a=user_enrollments"
                            class="importJson btn btn-dark pull-right ml10"><i class="fa fa-download"></i> Import
                        Enrollments
                    </button>


                    <button type="button" data-toggle="modal" data-target="#bulkDelete"
                            class="btn btn-danger pull-right ml10"><i class="fa fa-upload"></i> Bulk Delete
                    </button>

                    <a href="javascript:;" class="btn btn-system pull-right ml10" data-toggle="modal"
                       data-target="#merge">
                        <i class="fa fa-arrows-h"></i>
                        Merge
                    </a>

                    <a href="javascript:;" class="btn btn-success pull-right" data-toggle="modal"
                       data-target="#addUser">
                        <i class="fa fa-plus"></i>
                        Add User
                    </a>


                    <span class="panel-title">Accounts</span>
                    <p>View and manage all user accounts on <?= SITE_NAME ?></p>


                    <br/>
                    <br/>
                </div>
                <div class="panel-body pn">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Courses</th>
                                <th>Spend</th>
                                <th>Balance</th>
                                <th>Created</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Courses</th>
                                <th>Spend</th>
                                <th>Balance</th>
                                <th>Created</th>
                            </tr>
                            </tfoot>
                        </table>

                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('.datatable').DataTable({
                                    "processing": true,
                                    "serverSide": true,
                                    "order": [[0, "desc"]],
                                    "ajax": {
                                        "type": "GET",
                                        "url": "<?= SITE_URL ?>blume/datatables/accounts",
                                        "dataSrc": function (json) {

                                            return json.data;

                                        }
                                    },
                                    "drawCallback": function (settings) {

                                    }
                                });
                            });


                        </script>
                    </div>
                </div>
            </div>


            <!-- -------------- DEMO Break -------------- -->
            <div class="mv40"></div>


        </div>
        <!-- -------------- /Column Center -------------- -->

    </section>
    <!-- -------------- /Content -------------- -->
    <div class="deleteAccountReturn"></div>


    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add User</h4>
                </div>
                <form name="addNewItem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Which course do you want to enroll the new user onto?</label>
                            <select class="form-control" name="courseID">
                                <option value="">No Course</option>
                                <?php
                                foreach ($this->getAllCoursesWithoutHidden() as $course) {
                                    ?>
                                    <option value="<?= $course->id ?>"><?= $course->title ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Firstname</label>
                            <input type="text" name="firstname" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Lastname</label>
                            <input type="text" name="lastname" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="passwordConfirm" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Voucher Code <small>Only enter if you want to redeem one on behalf of this new
                                    user</small></label>
                            <input type="text" name="voucherCode" class="form-control"/>
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
        jQuery("form[name='addNewItem']").submit(function (e) {
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
    </script>

    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Edit Coupon</h4>
                </div>
                <form name="editItem">
                    <div class="modal-body">
                        <div id="ajaxEdit">
                            <p class="text-center">
                                <i class="fa fa-spin fa-spinner"></i>
                            </p>
                        </div>
                        <div id="returnStatusEdit"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery("form[name='editItem']").submit(function (e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-coupon",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#returnStatusEdit').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

    <div class="modal fade" id="addNewImport" tabindex="-1" role="dialog" aria-labelledby="addNewImport">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Import By ID</h4>
                </div>
                <form name="addNewImportItem">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Old ID</label>
                            <input type="text" id="user_old_id" name="user_old_id" class="form-control" required/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            $("#sidebar_left_toggle").trigger("click");
        });
        jQuery("form[name='addNewImportItem']").submit(function (e) {
            e.preventDefault();
            // $('textarea[name="text"]').html($('.summernote').code());
            //var formData = new FormData($(this)[0]);
            // alert(formData);
            // return false;
            var user_old_id = $("#user_old_id").val();
            $("#importingModal").modal();
            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=import&a=user_accounts&user_old_id=" + user_old_id,
                type: "GET",
                data: '',
                async: true,
                success: function (msg) {
                    location.reload();
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    </script>

    <div class="modal fade" id="bulkDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Bulk Delete Users/Accounts</h4>
                </div>
                <form name="bulkDelete">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Select CSV</label>
                            <input type="file" name="file" class="form-control"/>
                        </div>

                        <p>Required fields names for importing are as follows:</p>

                        <p>
                            <strong>EMAIL</strong>: the email address of the account you wish to delete<br/>
                        </p>

                        <p>Please only import the EMAIL column.</p>


                        <div id="return_statusDelete"></div>

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
        jQuery("form[name='bulkDelete']").submit(function (e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=delete-bulk-users",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#return_statusDelete').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

    <div class="modal fade" id="merge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Merge Accounts</h4>
                </div>
                <form name="merge">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Original Account ID</label>
                            <input type="text" name="oldID" class="form-control"/>
                        </div>

                        <div class="form-group">
                            <label>New Account ID</label>
                            <input type="text" name="newID" class="form-control"/>
                        </div>

                        <p>Input the ID's (not email addresses) of the accounts you want to merge.</p>

                        <p>
                            This will move all orders, courses, progress, rewards, notes, etc into the selected account
                            - then delete the original account automatically.
                        </p>

                        <p><strong>Only click the Merge button once, the process might take a few seconds.</strong></p>


                        <div id="return_statusMerge"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Merge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery("form[name='merge']").submit(function (e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            jQuery.ajax({
                url: "<?= SITE_URL ?>ajax?c=blumeNew&a=merge-accounts",
                type: "POST",
                data: formData,
                async: true,
                success: function (msg) {
                    jQuery('#return_statusMerge').html(msg);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>

    <div class="modal fade" id="exportBusinessEmails" tabindex="-1" role="dialog"
         aria-labelledby="exportBusinessEmails">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Export Business Accounts</h4>
                </div>
                <form name="exportBusinessEmails">
                    <div class="modal-body">
                        <label>Enter emails to receive the export csv</label>
                        <div class="input-list">
                            <a class="input-list--add btn btn-success mb-1">
                                <i class="fa fa-plus"></i>
                            </a>
                            <div class="input-list--container">
                                <div class="input-list--group">
                                    <input type="text" id="emails" name="emails" class="form-control" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function showBusinessAccountsExportToast(msg, status) {
            toastr.options.positionClass = "toast-bottom-left";
            toastr.options.closeDuration = 1000;
            toastr.options.timeOut = 5000;
            if (status === 'success') {
                toastr.success(msg, '')
            } else {
                toastr.error(msg, 'Oops')
            }
        }
        jQuery(document).on('click', '.input-list .input-list--add', function (e) {
            e.preventDefault();
            let parentList = jQuery(this).parent('.input-list').find('.input-list--container');
            let formGroup = jQuery(parentList).find('.input-list--group').first().clone();
            parentList.append(formGroup);
        });
        function resetBusinessAccountsExportForm(obj) {
            let parentFormGroup = jQuery(obj).find('.input-list .input-list--container .input-list--group');
            jQuery.each(parentFormGroup, function (index, element) {
                if (index > 0) {
                    jQuery(element).hide()
                }
            })
        }
        jQuery("form[name='exportBusinessEmails']").submit(function (e) {
            e.preventDefault();
            var emails = [];
            var form = this;
            var inputs = jQuery(this).find('input[name="emails"]');
            inputs.each(function() {
                var val = $(this).val();
                if (val && val !== '' && jQuery.inArray(val, emails) === -1) {

                    emails.push(val)
                }
            });
            if (!emails.length) {
                showBusinessAccountsExportToast('Please enter an email address', 'error')
                return;
            }
            var formData = new FormData();
            formData.set('emails', emails.join(','))
            jQuery.ajax({
                url: "<?= SITE_URL?>ajax?c=account&a=export-business-accounts-csv",
                type: "POST",
                data: formData,
                async: true,
                success: function (data) {
                    let dataObj = JSON.parse(data);
                    var message = 'Error exporting business accounts';
                    if (typeof dataObj !== 'object') {
                        showBusinessAccountsExportToast(message, 'error')
                        return;
                    }
                    if (dataObj.hasOwnProperty('message')) {
                        message = dataObj.message;
                    }
                    if (!dataObj.hasOwnProperty('success') || !dataObj.success) {
                        showBusinessAccountsExportToast(message, 'error')
                        return;
                    }
                    showBusinessAccountsExportToast(message, 'success')
                    resetBusinessAccountsExportForm(form)
                    jQuery('#exportBusinessEmails').modal('hide');
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    </script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<?php include BASE_PATH . 'blume.footer.base.php'; ?>