<?php
$pricing = ORM::for_table("coursePricing")->where("courseID", $course->id)->find_many();
?>

<table class="table footable" data-filter="#fooFilter" data-page-navigation=".pagination" data-page-size="50">
    <thead>
    <tr>
        <th>Currency</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php

    foreach($pricing as $price) {
        $currency = ORM::for_table("currencies")->find_one($price->currencyID);

        ?>
        <tr id="item<?= $price->id ?>" <?php if($currency->available == "0") { ?>style="opacity:0.5;"<?php } ?>>
            <td>
                <?= $currency->code ?>
                <?php if($price->available == "0") { ?>
                    <i class="fa fa-times" style="margin-left:5px;"></i>
                <?php } else {
                    ?>
                    <i class="fa fa-check" style="margin-left:5px;"></i>
                <?php
                } ?>
            </td>
            <td>
                <?= $currency->short ?><?= number_format($price->price, 2) ?>
            </td>
            <td>
                <a href="#" data-toggle="modal" data-target="#edit<?= $price->id ?>" class="label label-warning">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="javascript:;" onclick="deletePriceItem(<?= $price->id ?>);" class="label label-danger">
                    <i class="fa fa-times"></i>
                </a>
            </td>
        </tr>

        <div class="modal fade" id="edit<?= $price->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Edit Pricing Option</h4>
                    </div>
                    <form name="addNewItem<?= $price->id ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Price (<?= $currency->code.'/'.$currency->short ?>)</label>
                                <input type="text" name="price" class="form-control" value="<?= $price->price ?>" />
                            </div>
                            <div class="form-group">
                                <label>Is this course available in this currency?</label>
                                <select class="form-control" name="available">
                                    <option value="1">Yes</option>
                                    <option value="0" <?php if($price->available == "0") { ?>selected<?php } ?>>No</option>
                                </select>
                            </div>
                            <div id="returnStatusAddNew<?= $price->id ?>"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                        <input type="hidden" name="id" value="<?= $price->id ?>" />
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery("form[name='addNewItem<?= $price->id ?>']").submit(function(e) {
                e.preventDefault();
                // $('textarea[name="text"]').html($('.summernote').code());
                var formData = new FormData($(this)[0]);

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumePricing&a=edit-pricing-option",
                    type: "POST",
                    data: formData,
                    async: true,
                    success: function (msg) {
                        jQuery('#returnStatusAddNew<?= $price->id ?>').html(msg);
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

<script type="text/javascript">
    function deletePriceItem(id) {
        if (window.confirm("Are you sure you want to delete this pricing item?")) {
            $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumePricing&a=delete-pricing-option&id="+id);
            $("#item"+id).fadeOut();
        }
    }
</script>
