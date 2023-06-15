<?php

$item = ORM::for_table("courseModuleFaqs")->find_one($this->get["id"]);

$metaTitle = "Edit Course Module FAQ";

include BASE_PATH . 'blume.header.base.php';

?>
<form name="edit" class="allcp-form theme-primary">

    <input type="hidden" name="itemID" value="<?= $item->id ?>" />

    <!-- -------------- Content -------------- -->

    <section id="content" class="table-layout animated fadeIn">


        <div class="chute chute-center">



            <div id="return_status" style="position:relative;top:-26px;"></div>



            <div class="row" style="margin-top: -29px;margin-bottom: 17px;">

                <div class="col-xs-6">

                </div>

                <div class="col-xs-6">

                    <a href="<?= SITE_URL ?>blume/courses/modules/faqs" class="btn btn-system pull-right" style="margin-left:5px;">Back to FAQ's</a>
                    <button class="btn btn-success pull-right" style="margin-left:5px;">Update</button>
                </div>

            </div>





            <h4>Contents - <?= $item->title ?></h4>

            <div class="panel mb25 mt5">

                <div class="panel-body pn" style="margin-top:0px;">


                    <div class="panel-body pn of-h">

                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" class="form-control" name="question" value="<?= $item->question ?>" />
                        </div>


                        <hr />

                        <script src='<?= SITE_URL ?>assets/js/tinymce/tinymce.min.js'></script>
                        <script>
                            tinymce.init({
                                selector: '#mytextarea',
                                plugins: 'table link lists hr textcolor emoticons image imagetools media',
                                content_css : '/assets/css/tinymce.css',
                                height: '400'
                            });
                        </script>

                        <textarea name="contents" id="mytextarea" style="height:400px;"><?= $item->answer ?></textarea>

                        <input type="hidden" name="itemID" value="<?= $item->id ?>" />
                    </div>

                </div>

            </div>


            <div class="mv40"></div>





        </div>

        <!-- -------------- /Column Center -------------- -->



    </section>

</form>





<script type="text/javascript">

    jQuery("form[name='edit']").submit(function(e) {

        tinyMCE.triggerSave();

        e.preventDefault();


        // $('textarea[name="text"]').html($('.summernote').code());


        var formData = new FormData($(this)[0]);






        jQuery.ajax({

            url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-course-module-faq",

            type: "POST",

            data: formData,

            async: true,

            success: function (msg) {

                jQuery('#return_status').html(msg);

            },

            cache: false,

            contentType: false,

            processData: false

        });



    });





</script>



<!-- -------------- /Content -------------- -->

<?php include BASE_PATH . 'blume.footer.base.php'; ?>
