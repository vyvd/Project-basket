<?php
$css = array("course-category.css", "staff-training.css");

$pageTitle = "Review Course";
include BASE_PATH . 'header.php';

?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--course filters-->
        <section class="course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <h1 class="section-title text-center">Review Course</h1>
                    </div>
                </div>
            </div>
        </section>

        <!--review-course-->
        <section class="review-courses">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-2"></div>
                    <div class="col-12 col-md-12 col-lg-8 staff-form contact review-course">

                        <h4 class="text-left">Leave us a review of your course and get entered in to our monthly draw to win a £50 Amazon gift voucher!</h4>
                        <form name="reviewCourse" id="reviewCourse">
                            <p><em>All fields, including the image of yourself, are required.</em></p>

                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="First Name" name="firstname" value="<?= $this->user->firstname ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Last Name" name="lastname" value="<?= $this->user->lastname ?>">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email Address" name="email" value="<?= $this->user->email ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Your City" name="city">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="The course you studied was..." name="course" value="<?= $this->get["course"] ?>">
                            </div>
                            <div class="form-group">
                                 <label>Your Rating</label>
                                <div class="rating">
                                    <label>
                                        <input type="radio" name="rating" value="5" title="5 stars"> 5
                                    </label>
                                    <label>
                                        <input type="radio" name="rating" value="4" title="4 stars"> 4
                                    </label>
                                    <label>
                                        <input type="radio" name="rating" value="3" title="3 stars"> 3
                                    </label>
                                    <label>
                                        <input type="radio" name="rating" value="2" title="2 stars"> 2
                                    </label>
                                    <label>
                                        <input type="radio" name="rating" value="1" title="1 star"> 1
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Please upload an image of yourself...</label>
                                <input type="file" class="form-control" name="uploaded_file">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Please write a minimum of 50 words about your experience taking this course..." name="comments" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox terms">
                                    <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1">
                                    <label class="custom-control-label" for="terms">
                                        I agree that the information given here may be published on newskillsacademy.co.uk and/or our sister sites and social media pages, and accept that grammar mistakes will be rectified without prior notification. One winner will be picked at random each month and notified within 5 working days by email.
                                    </label>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <input type="submit" value="Submit Review" class="btn btn-primary btn-lg extra-radius">
                            </div>
                        </form>
                        <?php
                        $this->renderFormAjax("course", "review-course", "reviewCourse");
                        ?>
                        <script>
                            $('.rating input').change(function () {
                                var $radio = $(this);
                                $('.rating .selected').removeClass('selected');
                                $radio.closest('label').addClass('selected');
                            });
                        </script>
                    </div>




                </div>
            </div>
        </section>

        <br />
        <br />
        <br />

        <?php include BASE_PATH . 'learn-confidence.php'; ?>

        <?php include BASE_PATH . 'newsletter.php'; ?>

        <?php include BASE_PATH . 'featured.php'; ?>

        <?php include BASE_PATH . 'success-stories.php'; ?>

    </main>
    <!-- Main Content End -->

<?php include BASE_PATH . 'footer.php';?>