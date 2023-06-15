<?php
$pageTitle = "Learning Blog";

$breadcrumb = array(
    "Blog" => SITE_URL."blog"
);
$selectedCategory = "";

if(@$_GET['request']){
    $blogCategory = $this->controller->getCategoryBySlug($_GET['request']);
    if($blogCategory->id){
        $selectedCategory = $blogCategory->id;
        $pageTitle = $blogCategory->title;
        $breadcrumb[$blogCategory->title] = '';
    }
}
$css = array("course-category.css");

include BASE_PATH . 'header.php';

?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <br />
        <!--course filters-->
        <section class="course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                        <h1 class="section-title text-left">Learning Blog</h1>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6" style="display:none;">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control text-left" placeholder="Search" aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--courses listing-->
        <section id="blogPosts" class="courses-listing blog">
            <div class="container wider-container">
                <div class="row popularCourseboxes">
                    <div class="col-12 col-md-12 col-lg-9 ">
                        <div class="row" id="returnPosts">
                            <div v-if="loadingPosts" class="text-center col-12">
                                <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                            </div>
                            <div v-else-if="blogs.length" v-for="(blog, index) in blogs" class="col-12 col-md-6 col-lg-4">
                                <div class="category-box">
                                        <a :href="blog.url">
                                            <img :src="blog.image_url" alt="Popular" />
                                            <div class="Popular-title-bottom">{{blog.title}}</div>
                                        </a>
                                    </div>
                            </div>
                            <div v-else class="text-center col-12 col-md-6 col-lg-4">
                                <h5>No Blog found</h5>
                            </div>
                        </div>

                        <div class="row mb-4" >
                            <div v-if="loadMore==1" class="col-12 text-center all-course-btn">
                                <button type="button" @click="loadMoreBlogs()" class="btn btn-primary btn-lg extra-radius">Load More</button>
                            </div>
                            <div v-else-if="loadingMore" class="text-center col-12">
                                <i class="fa fa-spin fa-spinner frontEndLoading"></i>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 col-md-12 col-lg-3 left-filters blog-right">
                        <div class="white-box">
                            <div id="accordion">
                                <div class="card">
                                    <div class="card-header" id="category">
                                        <h5 class="mb-0">
                                            <a class="filter-title" data-toggle="collapse" data-target="#categoryData" aria-expanded="true" aria-controls="categoryData">
                                                Category
                                            </a>
                                        </h5>
                                    </div>

                                    <form name="refinePosts" id="blogForm">
                                        <div id="categoryData" class="collapse show" aria-labelledby="category" data-parent="#accordion">
                                            <div class="card-body">
                                                <ul class="custom-control">
                                                    <li class="<?php if(!isset($_GET['request'])){ echo "active";}?>">
                                                        <a href="<?= SITE_URL.'blog/';?>">
                                                            All Articles
                                                        </a>
                                                    </li>
                                                    <?php
                                                    foreach($this->controller->getCategories() as $category) {
                                                        ?>
                                                            <li class="<?php echo (@$selectedCategory &&  $category->id == $selectedCategory) ? 'active' : '';?>">
                                                                <a href="<?= SITE_URL.'blog/category/'.$category->slug;?>" id="<?= $category->slug?>">
                                                                    <?= $category->title ?>
                                                                </a>
                                                            </li>

                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </form>
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
                    loadingPosts: false,
                    loadingMore: false,
                    blogs: [],
                    limit: 15,
                    offset: 0,
                    loadMore: 0,
                    selectedCategories: ['<?php echo $selectedCategory;?>']
                }
            },
            methods: {
                getBlogs(loadMore = false) {
                    if(loadMore == true){
                        this.loadMore = 0;
                        this.loadingMore = true;
                    }else{
                        this.loadingPosts = true;
                    }
                    that = this;
                    const url = "<?= SITE_URL ?>ajax?c=blog&a=getBlogsJson";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            categoryIDs: this.selectedCategories,
                            offset: this.offset,
                            limit: this.limit
                        },
                        success: function(response){

                            response = JSON.parse(response);

                            if(loadMore == true){
                                that.loadingMore = false;
                                response.data.blogs.forEach(function (blog) {
                                    that.blogs.push(blog);
                                });
                            }else{
                                that.loadingPosts = false;
                                that.blogs = response.data.blogs;
                            }

                            that.loadMore = response.data.loadMore;
                            console.log(response);
                        },
                        error: function(xhr, status, error){
                            console.error(xhr);
                        }
                    });
                },
                loadMoreBlogs() {
                    that = this;
                    that.offset = this.offset + this.limit;
                    this.getBlogs(true);
                },
                filterCategories: function () {
                    that = this;
                    $(".filterOptionItem").each(function(){
                        if($(this).prop('checked')){
                            that.selectedCategories.push( $(this).val() );
                        }
                    });
                    //console.log(that.selectedCategories);
                    that.getBlogs();
                },
            },
            beforeMount() {

            },
            mounted(){
                this.getBlogs();
            },

        })

        const vm = app.mount('#blogPosts')
    </script>
<?php include BASE_PATH . 'footer.php';?>