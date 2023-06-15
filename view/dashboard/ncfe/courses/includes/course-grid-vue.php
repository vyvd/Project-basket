<div class="category-box">
    <div class="img" :style="{ backgroundImage: 'url(' + course.imageUrl + ')' }"></div>
    <div class="Popular-title-top">
        <div class="progress">
            <div class="progress-bar bg-secondary" :class="course.percComplete == 0 ? 'zero' : ''" role="progressbar" :style="{width: course.percComplete + '%'}" :aria-valuenow="course.percComplete" aria-valuemin="0" aria-valuemax="100">{{course.percComplete}}%</div>
        </div>
    </div>
    <div class="Popular-title-bottom">
        <h5>{{course.courseTitle}}</h5>
        <div v-if="course.completed==1" class="row courseBtnRow">
            <div class="col-6">
                <a class="btn btn-outline-light" :href="course.courseUrl">{{course.btnLabel}}</a>
            </div>
            <div class="col-6">
                <a class="btn btn-outline-light" :href="course.certificateUrl" target="_blank"><i class="fa fa-file"></i> Certificate</a>
            </div>
        </div>
        <a v-else class="btn btn-outline-light" :href="course.courseUrl">{{course.btnLabel}}</a>
    </div>
    <div v-if="course.activated==0" class="inactive-user-course">
        <h2>Awaiting Activation</h2>
        <p>Please allow 1 working day</p>
    </div>
    <div v-else-if="course.trainingCourseDisable" class="inactive-user-course">
        <h2>Available on completion of level 2</h2>
<!--        <p>Please allow 1 working day</p>-->
    </div>
</div>