<?php
require_once(__DIR__ . '/mediaController.php');

class blogController extends Controller
{
    /**
     * @var mediaController
     */
    protected $medias;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->medias = new mediaController();
    }

    public function mostRecent() {

        return ORM::for_table("blog")->order_by_desc("id")->find_one();

    }

    public function getAuthor($id) {

        return ORM::for_table("blumeUsers")->find_one($id);

    }

    public function formatDescription($description, $limit = 115) {

        $description = strip_tags($description);
        $description = substr($description, 0, $limit);

        return $description;

    }

    public function getPosts($offset, $limit) {

        return ORM::for_table("blog")
            ->order_by_desc("id")
            ->offset($offset)
            ->limit($limit)
            ->find_many();

    }
    public function getBlogPosts($offset, $limit, array $categoryIDs = null)
    {
        $blogsIDs = [];
        if($categoryIDs){
            $cIDs = $this->getBlogIDsByCategoryIDs($categoryIDs);
            if(count($cIDs)){
                $i = 0;
                foreach ($cIDs as $cID){
                    $blogsIDs[$i] = $cID['blog_id'];
                    $i++;
                }
            }
        }

        $posts = ORM::for_table("blog")
            ->where_null('courseID')
            ->where_id_in($blogsIDs)
            ->where_lt("whenAdded", date('Y-m-d').' 00:00:00')
            ->order_by_desc("id")
            ->offset($offset)
            ->limit($limit)
            ->find_array();
        
        return $posts;
    }
    public function getBlogPostsCount(array $categoryIDs = null)
    {
        $blogsIDs = [];
        if($categoryIDs){
            $cIDs = $this->getBlogIDsByCategoryIDs($categoryIDs);
            if(count($cIDs)){
                $i = 0;
                foreach ($cIDs as $cID){
                    $blogsIDs[$i] = $cID['blog_id'];
                    $i++;
                }
            }
        }

        return ORM::for_table("blog")
            ->where_null('courseID')
            ->where_lt("whenAdded", date('Y-m-d').' 00:00:00')
            ->where_id_in($blogsIDs)
            ->count();
    }
    public function getBlogIDsByCategoryIDs($categoryIDs)
    {
        return ORM::for_table("blogCategoryIDs")
            ->where_in('category_id', $categoryIDs)
            ->find_array();
    }

    public function getBlogArticle() {

        return ORM::for_table("blog")->where("slug", $_GET["request"])->find_one();

    }

    public function getCategories() {

        return ORM::for_table("blogCategories")->order_by_asc("title")->find_many();

    }

    public function refinePosts() {


        $posts = ORM::for_table("blog")->where_in("category", $this->post["categories"])->find_many();

        if($this->post["categories"] == "") {
            $posts = ORM::for_table("blog")
                ->order_by_desc("id")
                ->limit(99)
                ->find_many();
        }

        foreach($posts as $post) {

            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="category-box">
                    <a href="<?= SITE_URL ?>blog/<?= $post->slug ?>">
                        <img src="<?= SITE_URL ?>assets/cdn/blog/<?= $post->image ?>" alt="Popular" />
                        <div class="Popular-title-bottom"><?= $post->title ?></div>
                    </a>
                </div>
            </div>
            <?php

        }

        if(count($posts) == 0) {
            ?>
            <div class="col-12">
                <br />
                <br />
                <br />
                <p class="text-center">There are no posts within the selected category/categories.</p>
            </div>
            <?php
        }

    }

    public function getCategoryByOldId(int $oldId)
    {
        $item = ORM::for_table("blogCategories")
            ->where('oldID', $oldId)
            ->find_one();
        return $item;
    }

    public function saveBlog(array $input)
    {

        $data = array(
            'title'       => $input["title"],
            'contents'    => $input["contents"] ?? null,
            'slug'        => isset($input["slug"]) ? $input["slug"] : $this->createSlug($input["title"]),
            'whenUpdated' => isset($input["whenUpdated"]) ? $input["whenUpdated"] : date("Y-m-d H:i:s"),
        );

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("blog")->find_one($input['id']);

        } else { //For Create
            $item = ORM::for_table("blog")->create();
            $data['whenAdded']   = isset($input["whenAdded"]) ? $input["whenAdded"] : date("Y-m-d H:i:s");
        }



        if (isset($input["oldID"])) { //For Importing Data
            $data['oldID'] = $input["oldID"];
        }
        if (isset($input["courseID"])) { //For Course Blog
            $data['courseID'] = $input["courseID"];
        }
        if (isset($input["description"])) { //For Importing Data
            $data['description'] = $input["description"];
        }


        $item->set($data);
        $item->save();

        if (@$input['wpImage'] && ($this->medias->hasMedia(blogController::class, $item->id) === false)
        ) {
            $this->medias->saveWPImage($input['wpImage'],
                array('type' => blogController::class, 'id' => $item->id), 'main_image');
        }

        // Update Categories
        if ($input['categories']) {
            $this->updateBlogCategories($item->id, $input['categories']);
        }


        // redirect to edit course so modules, etc can be added
        return $item;
    }

    public function createSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function updateBlogCategories(int $blog_id, array $categories)
    {
        ORM::for_Table("blogCategoryIDs")->where("blog_id", $blog_id)->delete_many();
        if (count($categories) >= 1) {
            foreach ($categories as $category) {
                $item = ORM::for_table("blogCategoryIDs")->create();
                $item->set(array(
                    'blog_id'   => $blog_id,
                    'category_id' => $category,
                ));
                $item->save();
            }
        }
    }

    public function getBlogImage(int $courseId, $size = 'full')
    {
        $media = $this->medias->getMedia(blogController::class, $courseId, 'main_image');

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'], $size . '/' . $media['fileName'], $url);
                if(@getimagesize($url)){
                    //image exists!
                }else{
                    $url = $media['url'];
                }
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function getBlogCategories($blogId, $type = null)
    {

        $items = ORM::for_table("blogCategoryIDs")
            ->where("blog_id", $blogId)
            ->find_many();

        $categories = array();

        foreach ($items as $item) {

            $category = ORM::for_table("blogCategories")
                ->find_one($item->category_id);
            if($type == 'all'){
                $categories[] = $category;
            }else{
                $categories[$category->id] = $category->title;
            }

        }

        return $categories;

    }

    public function getBlogsJson() {
        $blogs = [];
        $loadMore = 0;
        $offset = $this->post['offset'];
        $limit = $this->post['limit'];
        $categoryIDs = $this->post['categoryIDs'];

        $total = $this->getBlogPostsCount($categoryIDs);

        if($total >= 1){
            $loadMore = ($offset + $limit) < $total ? 1 : 0;
            $posts = $this->getBlogPosts($offset, $limit, $categoryIDs);
            $i = 0;
            foreach ($posts as $post){

                if (strlen($post['title']) >= 70) {
                    $post['title'] = substr($post['title'], 0, 70). "... ";
                }

                $blogs[$i]['id'] = $post['id'];
                $blogs[$i]['title'] = $post['title'];
                $blogs[$i]['url'] = SITE_URL.'blog/'.$post['slug'];
                $blogs[$i]['image_url'] = $this->getBlogImage($post['id']);
                $i++;
            }
        }
        $data = [
            'blogs' => $blogs,
            'total' => $total,
            'loadMore' => $loadMore,
        ];

        echo json_encode(array(
            'status' => 200,
            'data'   => $data
        ));
        exit;
    }

    public function getCategoryBySlug($slug)
    {
        $item = ORM::for_table('blogCategories')->where('slug', $slug)->find_one();
        return $item;
    }
}