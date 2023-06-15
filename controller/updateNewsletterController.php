<?php
require_once(__DIR__ . '/courseController.php');
//require_once('moosendController.php');

class updateNewsletterController extends Controller {


    /**
     * @var accountController
     */
    protected $accounts;

    public function __construct()
    {

        $this->moosend = new moosendController();
        $this->updateNewsletter();

    }

    public function updateNewsletter() {

        echo 'Updating newsletter';

        $limit = isset($_GET['user_per_page']) ? intval($_GET['user_per_page']) : 10;
        $page_no = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = 0;

        if(!empty($page_no)) {
            $offset = ($page_no - 1) * $limit;
        }


        var_dump('page', $page_no);
        var_dump('offset', $offset);
        var_dump('limit', $limit);


        $moosend_users = ORM::for_table("moosend_to_update")->where_null('is_updated')->where_null('has_no_course')->where_null('no_account')->offset($offset)->limit($limit)->findMany();
        //$moosend_users = ORM::for_table("moosend_to_update")->where_null('is_updated')->where_null('has_no_course')->limit($limit)->findMany();

        //var_dump($moosend_users);
        //die();

        /*
        $users_array = array();

        foreach ($moosend_users as $user) {
            $users_array[] = $user->email;
        }


        var_dump($users_array);
        //die();

        //$users = ORM::for_table("accounts")->offset($offset)->limit($limit)->findMany();
        $users = ORM::for_table("accounts")->whereIn('email', $users_array)->findMany();
        var_dump($users);

        die();
        */

        foreach ($moosend_users as $user) {

            //var_dump("updating id: {$user->id}; email {$user->email}");
            var_dump("updating email {$user->email}");

            $user_data = ORM::For_table("accounts")->where("email", $user->email)->find_one();

            if( empty($user_data) ) {

                $moosend_entry = ORM::for_table("moosend_to_update")
                    ->where('email', $user->email)
                    ->find_one();

                $moosend_data = [
                    'no_account' => 1,
                ];

                $moosend_entry->set($moosend_data);
                $moosend_entry->save();

                continue;

            }

            $coursesAssigned = ORM::for_table("coursesAssigned")
                ->where("accountID", $user_data->id)
                ->find_one();

            var_dump('courseID', $coursesAssigned->courseID);
            var_dump('accountID', $coursesAssigned->accountID);

            if(empty($coursesAssigned->courseID)) {

                $moosend_entry = ORM::for_table("moosend_to_update")
                    ->where('email', $user->email)
                    ->find_one();

                $moosend_data = [
                    'has_no_course' => 1,
                ];

                $moosend_entry->set($moosend_data);
                $moosend_entry->save();

                continue;

            }


            $course_data = ORM::for_table("courses")->find_one($coursesAssigned->courseID);


            //var_dump('course_data', $course_data);
            //var_dump('user_data', $user_data);


            if (!empty($course_data) && !empty($user_data)) {

                $categories = array();

                $courseCategories = ORM::for_table("courseCategoryIDs")->where("course_id", $course_data->id)->find_many();

                foreach ($courseCategories as $cat) {

                    $catData = ORM::for_table("courseCategories")->find_one($cat->category_id);

                    array_push($categories, $catData->title);

                }

                $imploded_cat_names = implode(',', $categories);

                $custom_fields = array(
                    'First course Title' => $course_data->title,
                    'Course Category' => $imploded_cat_names,
                    'user_id' => $user_data->id,
                    'First name' => $user_data->firstname,
                    'Last name' => $user_data->lastname
                );

                var_dump($custom_fields);

                $this->moosend->updateNewsletterSubscriber($user_data->firstname, $user_data->email, $custom_fields, false);

                $moosend_entry = ORM::for_table("moosend_to_update")
                    ->where('email', $user->email)
                    ->find_one();

                $moosend_data = [
                    'is_updated' => 1,
                ];

                $moosend_entry->set($moosend_data);
                $moosend_entry->save();


            }


        }

    }


}