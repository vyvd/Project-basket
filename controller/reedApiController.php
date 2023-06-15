<?php
class reedApiController extends Controller {

    public function getNewAccounts() {

        $dateFrom = date("Y-m-d H:i:s", strtotime('-24 hours', time()));
        $dateTo = date("Y-m-d H:i:s");

        $username = "ba37e519-02c2-4ad5-865f-8d06863ef6e0";
        $password = "";
        $remote_url = 'https://www.reed.co.uk/courses/api/v4/providers/orders?from='.urlencode($dateFrom).'&to='.urlencode($dateTo).'&pageSize=200';

        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents($remote_url, false, $context);

        $json = json_decode($file, true);


        foreach($json["orders"] as $order) {


            // compile basic account info
            $email = $order["orderItems"][0]["student"]["email"];
            $firstname = $order["orderItems"][0]["student"]["firstName"];
            $lastname = $order["orderItems"][0]["student"]["lastName"];



            // check if this user already has an account
            $existing = ORM::for_table("accounts")->where("email", $email)->find_one();

            $userID = $existing->id;

            if ($existing->id == "") {

                // generate new password
                $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-_+@/%';
                $pass = array();
                $alphaLength = strlen($alphabet) - 1;
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }

                $password = implode($pass);

                // create account
                $item = ORM::for_table("accounts")->create();

                $item->firstname = $firstname;
                $item->lastname = $lastname;
                $item->email = $email;
                $item->password = password_hash($password, PASSWORD_BCRYPT);
                $item->set_expr("whenCreated", "NOW()");
                $item->set_expr("whenUpdated", "NOW()");
                $item->currencyID = "1";

                $item->save();

                $userID = $item->id();

                // send email with new details
                $message = '<p>Hi '.$firstname.',</p>
            <p>Thank you for your course purchase through Reed.</p>
            
            <p>We have created an account for you so you are able to study your course from any where, at any time. You can sign in with your email - '
                    .$email.' - and the following password - '
                    .$password.' </p>
                    
                    <p>We recommend changing your password when you first sign in.</p>';

                $message .= $this->renderHtmlEmailButton("My Courses",
                    SITE_URL.'dashboard/courses');

                $this->sendEmail($email, $message,
                    "Your course purchase from New Skills Academy, via Reed");

                // send email to helpscout
                $message = '<p>Hi there,</p>
            <p>The following user has just had their account automatically created via Reed:</p>
            
            <p>
            <strong>Firstname:</strong> '.$firstname.'<br />
            <strong>Lastname:</strong> '.$lastname.'<br />
            <strong>Email:</strong> '.$email.'<br />
</p>
                    ';


                $this->sendEmail("support@newskillsacademy.co.uk", $message, $email." (".$firstname." ".$lastname.") was just added automatically from Reed");


            }


            // each order item/course
            foreach($order["orderItems"] as $orderItem) {

                $courseID = $orderItem["providerProductRef"];


                $courseData = ORM::for_table("courses")->find_one($courseID);

                if($courseData->title == "") { // to cater for old ID's which may be used
                    $courseData = ORM::for_table("courses")->where("oldID", $courseID)->find_one();
                }

                // make sure this course is not already assigned to the account
                $existingAssigned = ORM::for_table("coursesAssigned")->where("courseID", $courseData->id)->where("accountID", $userID)->count();

                if($existingAssigned == 0) {

                    // assign course to user
                    if ($courseData->childCourses != "") { // if it has child courses, we need to assign those also
                        // assign main bundle
                        $item = ORM::for_table("coursesAssigned")->create();

                        $item->set(
                            array(
                                'courseID'  => $courseData->id,
                                'accountID' => $userID
                            )
                        );

                        $item->set_expr("whenAssigned", "NOW()");

                        $item->save();

                        $bundleID = $item->id(); // get assigned bundle ID

                        foreach (json_decode($courseData->childCourses) as $child) {

                            // assign inner bundle
                            $item = ORM::for_table("coursesAssigned")->create();

                            $item->set(
                                array(
                                    'courseID'  => $child,
                                    'accountID' => $userID,
                                    'bundleID'  => $bundleID
                                )
                            );

                            $item->set_expr("whenAssigned", "NOW()");

                            $item->save();

                        }

                    } else {

                        $item = ORM::for_table("coursesAssigned")->create();

                        $item->set(
                            array(
                                'courseID'  => $courseData->id,
                                'accountID' => $userID
                            )
                        );

                        $item->set_expr("whenAssigned", "NOW()");

                        $item->save();
                    }

                }



            }

        }

    }

}