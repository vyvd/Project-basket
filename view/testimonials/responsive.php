<?php
$css = array("testimonials.css", "single-course.css");
$pageTitle = "Testimonials";

$breadcrumb = array(
    "Testimonials" => '',
);

include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--page title-->
        <section class="course-title">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="section-title text-left">Success Stories</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--Page Content-->
        <section class="success-stories">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 single-course-content">

                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#textReviews">Text Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#videosReviews">Video Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#trustPilot">Trustpilot</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <!-- Text Reviews pane -->
                            <div id="textReviews" class="container wider-container tab-pane active">
                                <div v-if="loadingData" class="text-center col-12">
                                    <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                                </div>
                                <div v-else-if="testimonials.length" class="row testi-list grid" style="left:25px;">
                                    <div v-for="(testimonial, index) in testimonials" class="col-12 col-md-6 col-lg-4 text-review-box">
                                        <?php include ('includes/testimonial-grid-vue.php');?>
                                    </div>
                                </div>
                                <div v-else class="text-center col-12">
                                    <h5>No Review found!</h5>
                                </div>

                                <div class="row mb-4" >
                                    <div v-if="loadMore==1" class="col-12 text-center all-course-btn">
                                        <button type="button" @click="loadMoreTestimonials()" class="btn btn-primary btn-lg extra-radius">Load More</button>
                                    </div>
                                    <div v-else-if="loadingMore" class="text-center col-12">
                                        <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                                    </div>
                                </div>
                            </div>

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js" integrity="sha512-JRlcvSZAXT8+5SQQAvklXGJuxXTouyq8oIMaYERZQasB8SBDHZaUbeASsJWpk0UUrf89DP3/aefPPrlMR1h1yQ==" crossorigin="anonymous"></script>

                            <style>
                                .text-review-box {
                                    width: 31%;
                                }
                            </style>
                            <script>
                                $( document ).ready(function() {


                                    // $('.grid').masonry({
                                    //     itemSelector: '.text-review-box',
                                    //     //columnWidth: 70.5,
                                    //     //gutter: 1
                                    // });

                                    $(".testi-list").css("opacity", "1");


                                });
                            </script>

                            <!-- Video Reviews pane -->
                            <div id="videosReviews" class="container tab-pane fade">
                                <div class="row">
                                    <?php
                                    $items = ORM::for_table("testimonials")->where_not_null("video")->where("location", "p")->order_by_expr("RAND()")->find_many();

                                    foreach($items as $item) {
                                        ?>
                                        <div class="col-12 col-md-6 col-lg-4 video-box">
                                            <iframe src="https://player.vimeo.com/video/<?= $item->video ?>" width="100%" height="166" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>

                                            <div class="person"><?= $item->name ?></div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- Trust Pilot pane -->
                            <div id="trustPilot" class="container tab-pane fade">
                                <script type='text/javascript' src='//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js?ver=5.4'></script>
                                <!-- TrustBox widget - Carousel -->
                                <div class="trustpilot-widget" data-locale="en-GB" data-template-id="539adbd6dec7e10e686debee" data-businessunit-id="5b450fa2ad92290001bfac20" data-style-height="1400px" data-style-width="100%" data-theme="light" data-stars="5" data-schema-type="Organization">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

    <script src="<?= SITE_URL ?>assets/vendor/vue3/dist/vue.global.prod.js"></script>
    <script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
    <script>
        const app = Vue.createApp({
            data() {
                return {
                    loadingData: false,
                    loadingMore: false,
                    loadMore: 0,
                    testimonials: [],
                    offset: 0,
                    limit: 24,
                    order: 'whenAdded',
                    orderBy: 'desc',
                    documentWidth: 0,
                    masonryContainer: ''
                }
            },
            methods: {
                getTestimonials(loadMore = false) {
                    this.loadingMore = false;
                    this.loadingData = false;
                    this.loadMore = 0;
                    if(loadMore === true){
                        this.loadMore = 0;
                        this.loadingMore = true;
                    }else{
                        this.loadingData = true;
                    }
                    that = this;
                    const url = "<?= SITE_URL ?>ajax?c=testimonial&a=getTestimonialsJson";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            offset: this.offset,
                            limit: this.limit,
                            order: this.order,
                            orderBy: this.orderBy,
                        },
                        success: function(response){
                            response = JSON.parse(response);
                            if(loadMore === true){
                                that.loadingMore = false;
                                response.data.testimonials.forEach(function (course) {
                                    that.testimonials.push(course);
                                });
                                // $('.grid').masonry({
                                //     itemSelector: '.text-review-box',
                                //     //columnWidth: 70.5,
                                //     //gutter: 1
                                // });
                            }else{
                                that.loadingData = false;
                                that.testimonials = response.data.testimonials;
                                $('.grid').masonry({
                                    itemSelector: '.text-review-box',
                                    //columnWidth: 70.5,
                                    //gutter: 1
                                });
                            }

                            that.loadMore = response.data.loadMore;
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                loadMoreTestimonials() {
                    that = this;
                    that.offset = this.offset + this.limit;
                    this.getTestimonials(true);
                },
            },
            beforeMount() {

            },
            mounted(){
                this.getTestimonials();
                // this.masonryContainer = $('.testi-list').masonry({
                //     //columnWidth: 150,
                //     itemSelector: '.text-review-box',
                //     //gutter: 20,
                //     //isFitWidth: true
                // });
            },

        })

        const vm = app.mount('#textReviews')
    </script>
<?php include BASE_PATH . 'footer.php';?>