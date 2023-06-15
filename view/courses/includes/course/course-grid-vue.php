<div class="category-box single_course">
    <div class="img" :style="{ backgroundImage: 'url(' + course.image_url + ')' }"></div>

    <div class="Popular-title-top"><i class="far fa-user"></i> {{course.total_students}}</div>
    <div class="Popular-title-bottom">{{course.title}} <h3 v-html="course.price"></h3></div>
    <div class="popular-box-overlay">
        <p><strong class="nsa_course_title">{{course.title}}</strong></p>
        <div class="popular-overlay-btn"><a :href="course.url" class="btn btn-outline-primary btn-lg extra-radius">{{course.total_modules}} {{course.module_text}}</a></div>
        <?php if($_SESSION["affiliateDiscount"] == "") { ?>
            <!--<div class="popular-overlay-btn"><a :href="course.url" class="btn btn-outline-primary btn-lg extra-radius">0% Finance</a></div>-->
        <?php } ?>
        <h3 class="course_price" v-html="course.price"></h3>
        <div class="popular-overlay-btn-btm">
            <a class="btn btn-outline-primary btn-lg extra-radius nsa_course_more_info" :href="course.url" role="button">More Info</a>&nbsp;&nbsp;
            <a class="btn btn-outline-primary btn-lg extra-radius start-course-button nsa_add_to_cart_btn" :data-course-id="course.id" :data-course-oldid="course.oldID" :data-oldproductid="course.productID" :data-course-cats="course.categories" :data-course_type="course.course_type" href="javascript:;" role="button" @click="addToCart(course.id)">Add to Cart</a>
            <a class="saveHeart" :class="['saveCourse' + course.id, {'active' : userSavedCourses.includes(course.id) == true}]" style="top: 5px;right: 6px;font-size: 25px;color: #cf0e0e;"
               href="javascript:;" role="button" @click="saveCourse(course.id)">
                <i class="far fa-heart"></i>
            </a>
            <p class="learningTypes" v-html="course.learningTypes"></p>
            <span class="ratingSmall" v-html="course.rating"></span>
        </div>
    </div>
</div>