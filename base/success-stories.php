<?php
require_once (APP_ROOT_PATH . 'controller/testimonialController.php');

$this->testimonials = new testimonialController();
?>
<section class="customer-says" <?php if($currency->code != "GBP") { ?>style="background:#22758f;"<?php } ?>>
    <div class="container wider-container padded-sec">
        <div class="row text-center">
            <div class="col-12">
                <h4 class="section-title"> What our students say about us...</h4>
                <div id="reviewsSlider" class=" reviewsSlider owl-carousel" >
                    <?php
                    $testimonials = ORM::for_table("testimonials")->where("location", "f")->order_by_expr("RAND()")->limit(8)->find_many();
                    ?>
                        <?php
                        $count = 0;
                        foreach($testimonials as $testimonial) {
                            $testimonialImage = $this->testimonials->getTestimonialImage($testimonial->id, "full");
                            ?>
                            <!--Slide 1-->
                            <div class="item <?php if($count == 0) { ?>active<?php } ?>">
                                <?php
                                if(@$testimonialImage){
                                    ?>
                                    <img src="<?= $this->testimonials->getTestimonialImage($testimonial->id, "full") ?>" class="reviewBottomImg" alt="<?= $testimonial->name ?>" />
                                    <?php
                                }
                                ?>
                                <div class="col-12 review-col">
                                    <p><?= nl2br($testimonial->testimonial) ?></p>
                                    <p class="review-customer"><?= $testimonial->name ?></p>
                                </div>
                            </div>
                            <?php
                            $count ++;
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function (){
        $('.reviewsSlider.owl-carousel').owlCarousel({
            loop:true,
            autoplay: true,
            margin:10,
            nav:true,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        })
    });
</script>