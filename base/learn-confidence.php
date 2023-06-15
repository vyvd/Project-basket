<section class="learn-with-confidence-sec padded-sec">
    <div class="container wider-container learn-with-confidence">
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Learn with confidence...</h4>
                <div id="learnConfidenceSlider" class="learnConfidenceSlider owl-carousel">
                    <div class="item">
                        <img src="<?= SITE_URL ?>assets/images/approved/cpd-courses.png" alt="cpd" />
                    </div>
                    <div class="item">
                        <img src="<?= SITE_URL ?>assets/images/approved/CMA-courses.png" alt="cma" />
                    </div>
                    <?php if($currency->code == "GBP") { ?>
                    <div class="item">
                        <img src="<?= SITE_URL ?>assets/images/approved/uklrp.png" alt="uklrp" />
                    </div>
                    <?php } ?>
                    <div class="item">
                        <img src="<?= SITE_URL ?>assets/images/approved/rospa.png" alt="rospa" />
                    </div>
                    <?php if($currency->code == "GBP") { ?>
                    <div class="item">
                        <img src="<?= SITE_URL ?>assets/images/approved/xo-student-discounts.png" alt="xo" />
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if($currency->code == "GBP") { ?>
<script>
    $(document).ready(function (){
        $('.learnConfidenceSlider.owl-carousel').owlCarousel({
            loop:false,
            autoplay: true,
            margin:10,
            nav:true,
            responsive:{
                0:{
                    items:2
                },
                600:{
                    items:3
                },
                1000:{
                    items:5
                }
            }
        })
    });
</script>
<?php } else {
    ?>
    <style>
        #learnConfidenceSlider .item {
            text-align:center;
        }
    </style>
    <script>
        $(document).ready(function (){
            $('.learnConfidenceSlider.owl-carousel').owlCarousel({
                loop:false,
                autoplay: true,
                margin:10,
                nav:true,
                responsive:{
                    0:{
                        items:2
                    },
                    600:{
                        items:3
                    },
                    1000:{
                        items:3
                    }
                }
            })
        });
    </script>
    <?php
} ?>