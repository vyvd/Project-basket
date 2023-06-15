<?php
$this->setControllers(array("course"));

$article = $this->controller->getBlogArticle();
$articleCategories = $this->controller->getBlogCategories($article->id, 'all');

if($article->title == "") {
    $this->force404(); // force 404 error as the article does not exist
}

$css = array("course-category.css", "blog.css");

$breadcrumb = array(
    "Blog" => SITE_URL.'blog'
);

if(@$articleCategories[0]->id){
    $breadcrumb[$articleCategories[0]->title] = SITE_URL.'blog/category/'.$articleCategories[0]->slug;
}
$breadcrumb[$article->title] = '';
//echo "<pre>";
//print_r($articleCategories);
//die;

ob_start();
include("includes/courseblock.php");
$courseblock = ob_get_clean();

$pageTitle = $article->title;
include BASE_PATH . 'header.php';
?>

    <!-- Main Content Start-->
    <main role="main" class="regular">

        <!--course filters-->
        <section class="course-filters">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-6">
                        <h2 class="section-title text-left">Learning Blog</h2>
                    </div>
                    <div class="col-12 col-md-12 col-lg-6">
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
        <section class="courses-listing">
            <div class="container wider-container">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-9 blog-single">
                        <div class="blog-banner">
                            <img src="<?= $this->controller->getBlogImage($article->id) ?>" alt="<?= $article->title ?>" />
                        </div>
                        <div class="blog-texts">
                            <h1><?= $article->title ?></h1>

                            <div class="author">
                                <img src="<?= SITE_URL ?>assets/images/lizzieLearn.png" alt="Lizzie Learn" />
                                <div class="content">
                                    Written by Lizzie Learn
                                    <span>On <?= date('jS M Y', strtotime($article->whenAdded)) ?></span>
                                </div>
                            </div>

                            <?= str_replace("[courseblock]", $courseblock, $article->contents) ?>

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
                                                    <li class="">
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
                                    <?php
                                    $this->renderFormAjax("blog", "refine-posts", "refinePosts", "#returnPosts");
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="white-box">
                            <div id="accordion2">
                                <div class="card">
                                    <div class="card-header" id="type">
                                        <h5 class="mb-0">
                                            <a class="filter-title" data-toggle="collapse" data-target="#typeData" aria-expanded="false" aria-controls="typeData">
                                                Recent Articles
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="typeData" class="collapse show" aria-labelledby="type" data-parent="#accordion2">
                                        <div class="card-body">
                                            <?php
                                            $posts = $this->controller->getBlogPosts(0, 5); // offset, limit
                                            foreach($posts as $post) {

                                                if (strlen($post['title']) >= 40) {
                                                    $post['title'] = substr($post['title'], 0, 40). "... ";
                                                }

                                                ?>
                                                <div class="related-articles" style="display:block;">
                                                    <div style="width:70px;height:75px;border-top-left-radius:15px;border-bottom-left-radius:15px;background-image:url('<?= $this->controller->getBlogImage($post['id']) ?>');background-size:cover;background-position:center center;display:inline-block;vertical-align: middle;"></div>
                                                    <a href="<?= SITE_URL ?>blog/<?= $post['slug'] ?>" style="display: inline-block;width: calc(100% - 98px);vertical-align: middle;"><?= $post['title'] ?></a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
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


<?php include BASE_PATH . 'footer.php';?>