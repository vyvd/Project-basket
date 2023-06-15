<?php if($currency->code == "GBP") { ?>
<div class="container wider-container padded-sec as-featured">
    <div class="row text-center">
        <h4 class="section-title">As Featured In...</h4>
        <div id="asFeaturedSlider" class="carousel slide asFeaturedSlider" data-ride="false">
            <div class="carousel-inner">
                <!--Slide 1-->
                <div class="carousel-item active">
                    <div class="col-12 text-center">
                        <a href="#"><img src="<?= SITE_URL ?>assets/images/bbc.png" alt="Featured" /></a>
                        <a href="#"><img src="<?= SITE_URL ?>assets/images/guardian.png" alt="Featured" /></a>
                        <a href="#"><img src="<?= SITE_URL ?>assets/images/itv.png" alt="Featured" /></a>
                        <a href="#"><img src="<?= SITE_URL ?>assets/images/vogue.png" alt="Featured" /></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php } ?>