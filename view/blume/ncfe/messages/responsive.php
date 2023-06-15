<?php

$metaTitle = "Messages";
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
                <span class="panel-title">Messages</span>
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
                            <th>Student</th>
                            <th>Tutor</th>
                            <th>Subject</th>
                            <th>Date/Time</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $items = ORM::for_table("messages")
                            ->where_null("messageQueueID")
                            ->where("csApproved", "0")
                            ->order_by_desc("whenSent")
                            ->find_many();

                        foreach($items as $item) {
                            $student = ORM::for_table("accounts")->find_one($item->userID);
                            $tutor = ORM::for_table("accounts")->find_one($item->recipientID);
                            ?>
                            <tr id="item<?= $item->id ?>">
                                <td>
                                    <?= $student->firstname.' '.$student->lastname ?>
                                </td>
                                <td>
                                    <?= $tutor->firstname.' '.$tutor->lastname ?>
                                </td>
                                <td>
                                    <?= $item->subject ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($item->whenSent)) ?>
                                </td>
                                <td>
                                    <a href="javascript:;" class="label label-warning" data-toggle="modal" data-target="#edit<?= $item->id ?>">
                                        Action
                                    </a>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit<?= $item->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Action Message</h4>
                                        </div>
                                        <form name="addNewItem<?= $item->id ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Subject</label>
                                                    <input type="text" disabled class="form-control" value="<?= $item->subject ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>Message</label>
                                                    <textarea name="message" rows="5" disabled class="form-control"><?= $item->message ?></textarea>
                                                </div>
                                                <div id="returnStatusAddNew<?= $item->id ?>"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onclick="deleteItem(<?= $item->id ?>);" class="btn btn-danger" data-dismiss="modal">Delete</button>
                                                <button type="submit" class="btn btn-primary">Approve</button>
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
                                        url: "<?= SITE_URL ?>ajax?c=blumeNcfe&a=action-message&id=<?= $item->id ?>",
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
        if (window.confirm("Are you sure you want to delete this message?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumeNcfe&a=delete-message&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>
=
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
