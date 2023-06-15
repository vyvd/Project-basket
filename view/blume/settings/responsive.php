<?php
$title = ORM::for_table("siteSettings")->where("name", "home_title")->find_one();
$desc = ORM::for_table("siteSettings")->where("name", "home_description")->find_one();
$tags = ORM::for_table("siteSettings")->where("name", "home_tags")->find_one();
$banner = ORM::for_table("siteSettings")->where("name", "topBannerText")->find_one();

$metaTitle = "Settings";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">

        <form name="editSettings">
            <div class="panel mb25 mt5">
                <div class="panel-heading">
                    <span class="panel-title hidden-xs"> Settings</span>
                </div>
                <div class="panel-body pn">
                    <div class="row">

                        <?php
                        $settings = array(
                                "Facebook" => "facebook",
                                "Twitter" => "twitter",
                                "Instagram" => "instagram",
                                "YouTube" => "youtube",
                                "Printed Certificate Price" => "printed_cert_price",
                                "Certificate Print Email" => "cert_print_email",
                                "Staff Training Form Email" => "staff_training_email",
                                "Recommend A Friend Discount (%)" => "raf_discount",
                                "Optionally show an urgent message on support pages" => "status_message",
                                "Study Group URL" => "study_group_url",
                                "Is the subscription balance offer currently active?" => "subBalanceOfferActive",
                                "Subscription balance offer message, which appears on site" => "subBalanceOfferMessage",
                                "How much balance should be added when someone subscribes annually?" => "subBalanceOfferAmount",
                        );

                        foreach($settings as $key => $value) {

                            ?>
                            <div class="col-xs-12">
                                <div class="section mb10 allcp-form theme-primary">
                                    <label for="name21" class="field">
                                        <label style="margin-bottom:5px;"><?= $key ?></label>
                                        <input type="text" name="<?= $value ?>" id="name21" class="event-name gui-input br-light light" value="<?= $this->controller->getSetting($value)->value ?>" />
                                    </label>
                                </div>
                            </div>
                            <?php

                        }
                        ?>


                        <div class="col-xs-12">
                            <div class="section mb10 allcp-form theme-primary">
                                <label for="name21" class="field">
                                    <label style="margin-bottom:5px;">Which course should be given for free when someone signs up to the newsletter?</label>
                                    <select name="newsletter_course" id="name21" class="event-name gui-input br-light light">
                                        <?php
                                        foreach(ORM::for_table("courses")->order_by_asc("title")->find_many() as $course) {
                                            ?>
                                            <option value="<?= $course->id ?>" <?php if($this->controller->getSetting("newsletter_course")->value == $course->id) { ?>selected<?php } ?>><?= $course->title ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>


                    <br />
                    <br />
                    <br />
                    <input type="submit" class="btn btn-block btn-system" value="Update" />

                    <div id="return_status"></div>

                </div>
            </div>
        </form>

        <script type="text/javascript">
            jQuery("form[name='editSettings']").submit(function(e) {

                var formData = new FormData($(this)[0]);
                e.preventDefault();

                jQuery.ajax({
                    url: "<?= SITE_URL ?>ajax?c=blumeNew&a=edit-settings",
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
<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
