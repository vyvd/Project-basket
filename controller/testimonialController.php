<?php
require_once(__DIR__ . '/mediaController.php');

class testimonialController extends Controller
{

    /**
     * @var string
     */
    protected $table;

    /**
     * @var mediaController
     */
    protected $medias;


    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->medias = new mediaController();
        $this->table = 'testimonials';
    }


    public function getTestimonialImage(int $catId, $size = 'full')
    {
        $media = $this->medias->getMedia(testimonialController::class, $catId,
            'main_image');

        if (@$media['url']) {
            $url = $media['url'];
            if ($size != 'full') {
                $url = str_replace($media['fileName'],
                    $size.'/'.$media['fileName'], $url);
            }
        } else {
            $url = '';
        }

        return $url;
    }

    public function saveTestimonial(array $input)
    {


        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table($this->table)->create();
        }

        $data = array(
            'name' => $input["name"],
            'slug' => isset($input["slug"]) ? $input["slug"]
                : $this->createSlug($input["title"]),
            'testimonial' => $input["testimonial"] ?? null,
            'location' => $input["location"] ?? 'f',
            'whenAdded' => isset($input["whenAdded"]) ? $input["whenAdded"]
                : date("Y-m-d H:i:s"),
        );

        if (isset($input["oldID"])) { //For Importing Data
            $data['oldID'] = $input["oldID"];
        }


        $item->set($data);
        $item->save();


        if (@$input['wpImage']
            && ($this->medias->hasMedia(testimonialController::class, $item->id)
                === false)
        ) {
            $this->medias->saveWPImage($input['wpImage'],
                array(
                    'type' => testimonialController::class, 'id' => $item->id
                ), 'main_image');
        }


        // redirect to edit course so modules, etc can be added
        return $item;
    }

    public function getTestimonialsCount()
    {
        $testimonials = ORM::for_table('testimonials');

        $testimonials = $testimonials->count();

        return $testimonials;
    }

    public function getTestimonials($offset, $limit, $order, $orderBy)
    {
        $testimonials = ORM::for_table("testimonials")
            ->where_null("video")
            ->where("location", "p");

        $testimonials = $orderBy == 'desc'
            ? $testimonials->order_by_desc($order)
            : $testimonials->order_by_asc($order);

        $testimonials = $testimonials->offset($offset)
            ->limit($limit)
            ->find_array();

        return $testimonials;
    }

    public function getTestimonialsJson()
    {
        $testimonials = [];
        $loadMore = 0;
        $offset = $this->post['offset'];
        $limit = $this->post['limit'];
        $order = $this->post['order'];
        $orderBy = $this->post['orderBy'];

        $total = $this->getTestimonialsCount();

        if ($total >= 1) {
            $loadMore = ($offset + $limit) < $total ? 1 : 0;
            $posts = $this->getTestimonials($offset, $limit, $order, $orderBy);
            $i = 0;
            foreach ($posts as $post) {
                $testimonials[$i]['id'] = $post['id'];
                $testimonials[$i]['testimonial'] = $post['testimonial'];
                $testimonials[$i]['image_url'] = $this->getTestimonialImage($post['id']);
                $testimonials[$i]['name'] = $post['name'];
                $i++;
            }
        }
        $data = [
            'testimonials' => $testimonials,
            'total'        => $total,
            'loadMore'     => $loadMore,
        ];

        echo json_encode(array(
            'status' => 200,
            'data'   => $data
        ));
        exit;
    }

}