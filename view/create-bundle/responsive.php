<?php
//$this->setControllers(array("course"));

//$this->controller->validateOrderConfirmed();
$bundleCourses = $this->controller->getBundleCourses();
//echo "<pre>";
//print_r($bundleCourses);
//die;
$pageTitle = "Create Bundle";
$css = array("checkout.css");
include BASE_PATH . 'header.php';

?>
<link rel="stylesheet" type="text/css" href="<?= SITE_URL ?>assets/blume/js/plugins/select2/css/core.css"
      xmlns="http://www.w3.org/1999/html">


    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--Checkout-->
        <section class="checkout">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <h1 class="section-title text-left">Create your own Course Bundle</h1>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="white-box">
                            <div class="row m-2 m-md-0 ">
                                <div class="col-md-8 borderBlue ml-md-5 mb-3 ">
                                    <div class="createBundleSection">
                                        <form name="cartBundle">
                                            <h4>Choose Any 3 Courses from our site for only <?= $this->price(59);?> - Save upto <?= $this->price(240);?></h4>

                                            <?php
                                            for ($i = 1; $i<=3; $i++){
                                                ?>
                                                <div class="form-group mt-4 cartBundle cartBundle<?= $i;?>">
                                                    <select rel="<?= $i;?>" id="cartBundle<?= $i;?>" name="cartBundle<?= $i;?>" class="select2">
                                                        <option value="">Pick your <?= $i;?> course</option>
                                                        <?php
                                                        if(count($bundleCourses)){
                                                            foreach ($bundleCourses as $course) {
                                                                ?>
                                                                <option value="<?= $course->id?>"><?= $course->title?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <h5>Total <?php echo $this->price(59);?></h5>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <input type="submit" class="btn btn-secondary btn-lg extra-radius" value="PROCEED TO CHECKOUT">
                                                </div>
                                            </div>
                                        </form>
                                        <?php
                                        $this->renderFormAjax("cart", "cartBundleCheckout", "cartBundle");
                                        ?>
                                    </div>
                                </div>

                                <div class="col-md-3 borderBlue ml-md-4 popularCourseboxes">
                                    <h3 class="selectCoursesTitle">Selected Courses</h3>
                                    <p class="selectCoursesInfo text-center mt-4">Select a course from the dropdown list opposite</p>
                                    <div class="single-course-wrapper mt-3" id="customCourse1"></div>
                                    <div class="single-course-wrapper mt-3" id="customCourse2"></div>
                                    <div class="single-course-wrapper mt-3" id="customCourse3"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
        <div id="returnStatus">

        </div>

    </main>
    <!-- Main Content End -->
<?php include BASE_PATH . 'footer.php';?>
<script src='<?= SITE_URL ?>assets/blume/js/plugins/select2/select2.min.js'></script>
<script type="text/javascript">
    $('.select2').select2();

    $(document).ready(function (){
        $(".cartBundle select").on('change', function (){
            var courseID = $(this).val();
            var rel = $(this).attr('rel');

            const url = "<?= SITE_URL ?>ajax?c=course&a=selectBundleCourse";
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    courseID: courseID,
                },
                success: function(response){
                    response = JSON.parse(response);
                    $("#customCourse"+rel).html('<div class="category-box single_course"><div class="img" style="background-image: url('+ "'" +response.course.image_url + "'" +');"></div><div class="Popular-title-bottom">'+response.course.title+' </div><div class="popular-box-overlay"><p><strong class="nsa_course_title">'+response.course.title+'</strong></p><div class="popular-overlay-btn"><a href="'+response.course.url+'" class="btn btn-outline-primary btn-lg extra-radius">'+ response.course.total_modules +' '+ response.course.module_text +'</a></div><div class="popular-overlay-btn-btm"><a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info" href="'+response.course.url+'" role="button">More Info</a></div></div></div>')
                    $(".selectCoursesInfo").remove();
                    console.log(response);
                    return false;
                    if(loadMore === true){
                        that.loadingMore = false;
                        response.data.courses.forEach(function (course) {
                            that.courses.push(course);
                        });
                    }else{
                        that.loadingData = false;
                        that.courses = response.data.courses;
                    }
                    that.userSavedCourses = response.data.userSavedCourses;
                    that.loadMore = response.data.loadMore;
                    console.log(response);
                },
                error: function(xhr, status, error){
                    console.error(xhr);
                }
            });

        });
    });

</script>
<?php if( !empty($items) ): ?>


<?php endif; ?>
