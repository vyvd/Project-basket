<?php
$this->controller->deleteUserDuplicates(CUR_ID_FRONT);
$this->controller->checkBundleProgress(CUR_ID_FRONT);

$pageTitle = "My Courses";
include BASE_PATH . 'account-ncfe.header.php';

?>

    <section class="page-title">
        <div class="container">
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <section class="page-content" id="userCourses">
        <div class="container">


            <div class="loading-card col-12 text-center" v-if="loadingUserData">
                <i class="fa fa-spin fa-spinner" style="font-size:100px;margin-top:100px;color:#248CAB;"></i>
            </div>

            <div v-else class="row">

                <div class="col-12 regular-full">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-12 col-lg-4 no-padding courseFilterHover <?php if($this->get["filter"] == "completed") { ?>active<?php } ?>" onclick="parent.location='<?= SITE_URL ?>dashboard/courses?filter=completed'">
                            <div class="white-rounded dash-details">
                                <h1 class="text-center"><?= ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "1")->where("accountID", CUR_ID_FRONT)->count() ?></h1>
                                <h3 class="text-center">Completed Courses</h3>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4 no-padding courseFilterHover <?php if($this->get["filter"] == "active") { ?>active<?php } ?>" onclick="parent.location='<?= SITE_URL ?>dashboard/courses?filter=active'">
                            <div class="white-rounded dash-details">
                                <h1 class="text-center"><?= ORM::for_table("coursesAssigned")->where_null("bundleID")->where("completed", "0")->where("accountID", CUR_ID_FRONT)->count() ?></h1>
                                <h3 class="text-center">Active Courses</h3>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4 no-padding courseFilterHover <?php if($this->get["filter"] == "") { ?>active<?php } ?>" onclick="parent.location='<?= SITE_URL ?>dashboard/courses'">
                            <div class="white-rounded dash-details">
                                <h1 class="text-center"><?= ORM::for_table("coursesAssigned")->where_null("bundleID")->where("accountID", CUR_ID_FRONT)->count() ?></h1>
                                <h3 class="text-center">All My Courses</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 regular-full search-box">
                    <div class="row">
                        <input v-on:keyUp="searchCourseText" type="text" v-model="searchText" class="search-field" id="filter" placeholder="Search my courses...">
                        <a v-on:click="searchCourse" class="search-button" href="javascript:void(0);"><i class="fas fa-search" aria-hidden="true"></i></a>
                    </div>
                </div>



                <div class="col-12 regular-full popularCourseboxes">
                    <div class="row">
                        <div v-if="loadingData" class="text-center col-12">
                            <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                        </div>
                        <div  v-else-if="userCourses" v-for="(course, index) in userCourses" class="col-12 col-md-6 col-lg-4">
                            <?php include ('includes/course-grid-vue.php');?>
                        </div>
                        <div v-else class="text-center col-12">
                            <h5>You have not yet enrolled onto any courses!</h5>
                        </div>
                    </div>

                    <div class="row mb-4" >
                        <div v-if="loadMore==1" class="col-12 text-center all-course-btn">
                            <button type="button" @click="loadMoreUserCourses()" class="btn btn-primary btn-lg extra-radius">Load More</button>
                        </div>
                        <div v-else-if="loadingMore" class="text-center col-12">
                            <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!--    <script src="--><?//= SITE_URL ?><!--assets/vendor/vue3/dist/vue.global.prod.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>
    <script>
        $("#filter").keyup(function() {

            // Retrieve the input field text and reset the count to zero
            var filter = $(this).val(),
                count = 0;

            // Loop through the comment list
            $('.popularCourseboxes .col-lg-4').each(function() {


                // If the list item does not contain the text phrase fade it out
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).hide();

                    // Show the list item if the phrase matches and increase the count by 1
                } else {
                    $(this).show();
                    count++;
                }

            });

        });

    </script>
    <script>

        var app = new Vue({
            el: '#userCourses',
            data: {
                loadingUserData: false,
                loadingData: false,
                loadingMore: false,
                loadMore: 0,
                userCourses: [],
                accountID: '<?= CUR_ID_FRONT;?>',
                offset: 0,
                limit: 12,
                order: 'whenAdded',
                orderBy: 'desc',
                searchText: null,
                filter: "<?= $_GET['filter'] ?? null ?>"
            },
            methods: {
                getUserCourses: function(loadMore) {
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
                    const url = "<?= SITE_URL ?>ajax?c=course&a=getUserCoursesJson";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            accountID: this.accountID,
                            offset: this.offset,
                            limit: this.limit,
                            order: this.order,
                            orderBy: this.orderBy,
                            filter: this.filter,
                            searchText: this.searchText,
                        },
                        success: function(response){
                            response = JSON.parse(response);
                            if(loadMore === true){
                                that.loadingMore = false;
                                response.data.userCourses.forEach(function (course) {
                                    that.userCourses.push(course);
                                });
                                // $('.grid').masonry({
                                //     itemSelector: '.text-review-box',
                                //     //columnWidth: 70.5,
                                //     //gutter: 1
                                // });
                            }else{
                                that.loadingData = false;
                                that.userCourses = response.data.userCourses;
                            }

                            that.loadMore = response.data.loadMore;
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                loadMoreUserCourses: function() {
                    that = this;
                    that.offset = this.offset + this.limit;
                    this.getUserCourses(true);
                },
                searchCourse: function () {
                    that = this;
                    this.getUserCourses();
                },
                searchCourseText: function() {
                    that = this;
                    if(that.searchText.length >= 3 || that.searchText.length == 0){
                        this.getUserCourses();
                    }
                },
                importUserData: function() {
                    this.loadingUserData = true;
                    const url = "<?= SITE_URL ?>ajax?c=import&a=userData&account_id=<?= $this->user->id;?>&user_id=<?= $this->user->oldID;?>";
                    $.ajax({
                        type: "GET",
                        url: url,
                        success: function(response){

                            response = JSON.parse(response.data.status);

                            var status = response.data.status;
                            if (status == 200) {
                                location.reload();
                            }
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                    // axios.get(url)
                    // .then(response => {
                    //     var status = response.data.status;
                    //     if (status == 200) {
                    //         location.reload();
                    //     }
                    // });
                },
            },
            beforeMount: function() {
                <?php
                if(($this->user->oldID != "" || $this->user->oldID != null) && ($this->user->dataImported == 0)){
                ?>
                this.importUserData();
                <?php
                }
                ?>
            },
            mounted: function() {
                this.getUserCourses(false);
            },
        })
    </script>

<?php include BASE_PATH . 'account.footer.php';?>