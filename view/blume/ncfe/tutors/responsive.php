<?php

$metaTitle = "NCFE Tutors";
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
                <span class="panel-title">NCFE Tutors</span>
                <a href="#" data-toggle="modal" data-target="#addNew" class="btn btn-success pull-right">
                    Add New
                </a>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th>Students Assigned</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("accounts")->where("isTutor", "1")->order_by_desc("whenCreated")->find_many();

                        foreach($items as $item) {
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $item->firstname.' '.$item->lastname ?>
                                </td>
                                <td>
                                    <?= $item->email ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($item->whenCreated)) ?>
                                </td>
                                <td>
                                    <?= ORM::for_table("accounts")->where("tutorID", $item->id)->count(); ?>
                                    <a href="javascript:;" data-toggle="modal" data-target="#manageStudents<?= $item->id ?>" class="label label-system">
                                        View
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:;" class="label label-warning" data-toggle="modal" data-target="#edit<?= $item->id ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:;" class="label label-info" data-toggle="modal" data-target="#reassign<?= $item->id ?>" style="margin-left:5px;cursor:pointer;">
                                        Re-assign Students
                                    </a>
                                    <label class="label label-danger" onclick="deleteItem(<?= $item->id ?>);" style="margin-left:5px;cursor:pointer;">
                                        <i class="fa fa-times"></i>
                                    </label>
                                    <a href="<?= SITE_URL ?>ajax?c=blumeNew&a=access-users-account&id=<?= $item->id ?>" target="_blank" class="label label-success" style="margin-left:5px;cursor:pointer;">
                                        Access
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="manageStudents<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Manage Students</h4>
                                        </div>
                                            <div class="modal-body">

                                                <?php
                                                $this->setAlertInfo('Manage the students assigned to this tutor.');

                                                $students = ORM::for_table("accounts")->where("tutorID", $item->id)->find_many();

                                                foreach($students as $student) {

                                                    ?>
                                                    <p id="student<?= $student->id ?>">
                                                        <?= $student->firstname.' '.$student->lastname ?> (<?= $student->email ?>)
                                                    </p>
                                                    <?php

                                                }
                                                ?>


                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                    </div>
                                </div>
                            </div>


                            <div class="modal fade" id="reassign<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Re-assign Students</h4>
                                        </div>
                                        <form name="reassign<?= $item->id ?>">
                                            <div class="modal-body">

                                                <?php
                                                $this->setAlertInfo('This tool allows you re-assign all of '.$item->firstname.'\'s students to another tutor.');
                                                ?>

                                                <div class="form-group">
                                                    <label>Re-assign to:</label>
                                                    <select class="form-control" name="assignID">
                                                        <?php
                                                        foreach($items as $person) {

                                                            ?>
                                                            <option value="<?= $person->id ?>"><?= $person->firstname.' '.$person->lastname ?></option>
                                                            <?php

                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div id="returnStatusAddNewReassign<?= $item->id ?>"></div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Re-assign</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script type="text/javascript">
                                jQuery("form[name='reassign<?= $item->id ?>']").submit(function(e) {
                                    e.preventDefault();
                                    // $('textarea[name="text"]').html($('.summernote').code());
                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNcfe&a=reassign-students&id=<?= $item->id ?>",
                                        type: "POST",
                                        data: formData,
                                        async: true,
                                        success: function (msg) {
                                            jQuery('#returnStatusAddNewReassign<?= $item->id ?>').html(msg);
                                        },
                                        cache: false,
                                        contentType: false,
                                        processData: false
                                    });
                                });
                            </script>

                            <div class="modal fade" id="edit<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Tutor</h4>
                                        </div>
                                        <form name="addNewItem<?= $item->id ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Firstname</label>
                                                    <input type="text" name="firstname" class="form-control" value="<?= $item->firstname ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Lastname</label>
                                                    <input type="text" name="lastname" class="form-control" value="<?= $item->lastname ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?= $item->email ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Change Password (optional)</label>
                                                    <input type="password" name="password" class="form-control" />
                                                </div>
                                                <div id="returnStatusAddNew<?= $item->id ?>"></div>
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
                                jQuery("form[name='addNewItem<?= $item->id ?>']").submit(function(e) {
                                    e.preventDefault();
                                    // $('textarea[name="text"]').html($('.summernote').code());
                                    var formData = new FormData($(this)[0]);

                                    jQuery.ajax({
                                        url: "<?= SITE_URL ?>ajax?c=blumeNcfe&a=edit-tutor&id=<?= $item->id ?>",
                                        type: "POST",
                                        data: formData,
                                        async: true,
                                        success: function (msg) {
                                            jQuery('#returnStatusAddNew<?= $item->id ?>').html(msg);
                                        },
                                        cache: false,
                                        contentType: false,
                                        processData: false
                                    });
                                });
                            </script>

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

<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteItem(id) {
        if (window.confirm("Are you sure you want to delete this tutor? Please make sure all students have been re-assigned before deleting.")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNcfe&a=delete-tutor&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>

<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Tutor</h4>
            </div>
            <form name="addNewItem">
                <div class="modal-body">
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
                        <input type="email" name="email" class="form-control" />
                    </div>
                    <p><em>Their password will be automatically generated and emailed to them.</em></p>
                    <p><em>You'll be able to assign students to this tutor later on.</em></p>
                    <div id="returnStatusAddNew"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery("form[name='addNewItem']").submit(function(e) {
        e.preventDefault();
        // $('textarea[name="text"]').html($('.summernote').code());
        var formData = new FormData($(this)[0]);

        jQuery.ajax({
            url: "<?= SITE_URL ?>ajax?c=blumeNcfe&a=create-tutor",
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

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
