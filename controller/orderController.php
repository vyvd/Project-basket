<?php


class orderController extends Controller
{

//    /**
////     * @var mediaController
////     */
////    protected $medias;
////
////    /**
////     * @var mediaController
////     */
////    protected $quizzes;

    public function __construct()
    {
//        $this->medias = new mediaController();
//        $this->quizzes = new quizController();
    }

    public function getOrderByID($id)
    {
        return ORM::for_table('orders')->find_one($id);
    }

    public function getOrderByOldID($oldId, $isUsImport = '0')
    {
        return ORM::for_table('orders')
            ->where('oldId', $oldId)
            ->where('usImport', $isUsImport)
            ->find_one();
    }
    public function saveOrder(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("orders")->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table("orders")->create();
        }

        $data = array(
            'accountID'    => $input["accountID"],
            'email'        => $input["email"],
            'firstname'    => $input["firstname"],
            'lastname'     => $input["lastname"],
            'customerIP'   => $input["customerIP"],
            'status'       => $input["status"],
            'method'       => $input["method"],
            'method_title' => isset($input["method"]) ? $input["method"] : null,
            'couponID'     => isset($input["couponID"]) ? $input["couponID"] : null,
            'total'        => $input["total"],
            'vatRate'      => isset($input["vatRate"]) ? $input["vatRate"] : null,
            'vatAmount'    => isset($input["vatAmount"]) ? $input["vatAmount"] : null,
            'address1'     => isset($input["address1"]) ? $input["address1"] : null,
            'address2'     => isset($input["address2"]) ? $input["address2"] : null,
            'city'         => isset($input["city"]) ? $input["city"] : null,
            'postcode'     => isset($input["postcode"]) ? $input["postcode"] : null,
            'country'      => isset($input["country"]) ? $input["country"] : null,
            'dataImported' => 1,
            'usImport'     => isset($input["usImport"]) ? $input["usImport"] : '0',
        );

        if (isset($input["gifted"])) {
            $data['gifted'] = $input["gifted"];
        }
        if (isset($input["whenCreated"])) {
            $data['whenCreated'] = $input["whenCreated"];
        }
        if (isset($input["whenUpdated"])) {
            $data['whenUpdated'] = $input["whenUpdated"];
        }

        if (isset($input["oldID"])) { //For Importing Data
            $data['oldID'] = $input["oldID"];
        }

        $item->set($data);
        $item->save();

        // OrderItems
        if (@$input['items']) {
            foreach ($input['items'] as $orderItem) {
                $orderItem['orderID'] = $item->id;
                $orderItem['whenCreated'] = $item->whenCreated;
                $this->saveOrderItem($orderItem);
            }
        }

        return $item;
    }

    protected function saveOrderItem(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table("orderItems")->find_one($input['id']);
        } else { //For Create
            $item = ORM::for_table("orderItems")->create();
        }

        $itemData = [
            'orderID'  => $input['orderID'],
            'courseID' => $input['courseID'],
            'price'    => $input['price'],
            'qty'      => $input['qty'],
        ];

        if (isset($input["whenCreated"])) {
            $itemData['whenCreated'] = $input["whenCreated"];
        }

        if (isset($input["oldID"])) { //For Importing Data
            $itemData['oldID'] = $input["oldID"];
        }
        if (isset($input["usImport"])) { //For Importing Data
            $itemData['usImport'] = $input["usImport"];
        }

        $item->set($itemData);
        $item->save();

        return $item;
    }

    public function getOrderItemByOldID($oldId, $usImport = '0')
    {
        return ORM::for_table('orderItems')
            ->where('oldID', $oldId)
            ->where('usImport', $usImport)
            ->find_one();
    }

    public function getOrderItemsByOrderID($orderID)
    {
        return ORM::for_table('orderItems')
            ->where('orderID', $orderID)
            ->find_many();
    }

}