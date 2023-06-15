<?php
$currency = ORM::for_table("currencies")->find_one($this->get["id"]);

$metaTitle = "Currencies";
include BASE_PATH . 'blume.header.base.php';

$item = $currency;
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title"><?= $currency->code ?> - Catalogue Refs.</span>

                <a href="<?= SITE_URL ?>ajax?c=fb-catalog&a=generateCatalogAllCurrenciesDynamic" class="btn btn-success pull-right" target="_blank">
                    Generate Dynamic XML Catalogue
                </a>

            </div>
            <div class="panel-body pn">


                <form name="editCatRefs<?= $item->id ?>">


                    <style>
                        #refPrices .row {
                            margin-top:15px;
                        }
                    </style>



                    <div id="refPrices">
                        <?php
                        $count2 = 0;
                        $refItems = ORM::for_table("currenciesDynamicRefCatPricing")->where("currencyID", $currency->id)->order_by_desc("price")->find_many();

                        foreach($refItems as $refItem) {

                            $count2 ++;

                            ?>
                            <div class="row" id="refItem_<?= $count ?>">
                                <div class="col-xs-6">
                                    <label>
                                        If price equals...
                                    </label>
                                    <input type="text" class="form-control" name="price_<?= $count ?>" placeholder="0.00" value="<?= $refItem->price ?>" />
                                </div>
                                <div class="col-xs-6">
                                    <label>
                                        Append ref code...
                                    </label>
                                    <input type="text" class="form-control" name="ref_<?= $count ?>" placeholder="Enter ID..." value="<?= $refItem->ref ?>" />
                                </div>
                            </div>
                            <?php

                        }
                        ?>
                    </div>

                    <a href="javascript:;" onclick="addRefPrice()">
                        <i class="fa fa-plus" style="font-size: 20px;color: #c5db26;margin-left: 10px;margin-top: 18px;"></i>
                    </a>

                    <input type="hidden" name="refCount" id="refCount" value="<?= $count2 ?>" />

                    <script>
                        function addRefPrice() {

                            var count = $("#refCount").val();

                            var newCount = parseInt(count)+1;

                            $("#refCount").val(newCount);

                            $("#refPrices").append('<div class="row" id="refItem_'+newCount+'"> <div class="col-xs-6"> <label> If price equals... </label> <input type="text" class="form-control" name="price_'+newCount+'" placeholder="0.00" value="" /> </div> <div class="col-xs-6"> <label> Append ref code... </label> <input type="text" class="form-control" name="ref_'+newCount+'" placeholder="Enter ID..." value="" /> </div> </div>');

                        }
                    </script>



                    <div id="returnStatusCatRefs<?= $item->id ?>"></div>


                    <hr />


                    <button type="submit" class="btn btn-primary">Update</button>

                </form>


                <script type="text/javascript">
                    jQuery("form[name='editCatRefs<?= $item->id ?>']").submit(function(e) {

                        e.preventDefault();

                        var formData = new FormData($(this)[0]);

                        jQuery.ajax({
                            url: "<?= SITE_URL ?>ajax?c=blumePricing&a=edit-currency-cat-refs&id=<?= $item->id ?>",
                            type: "POST",
                            data: formData,
                            async: true,
                            success: function (msg) {
                                jQuery('#returnStatusCatRefs<?= $item->id ?>').html(msg);
                            },
                            cache: false,
                            contentType: false,
                            processData: false
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
<div id="deleteProduct"></div>
<script type="text/javascript">
    function deleteProduct(id) {
        $("#deleteProduct").load("<?= SITE_URL ?>ajax?c=blumePricing&a=delete-currency&id="+id);
        $("#product"+id).fadeOut();
    }
</script>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
