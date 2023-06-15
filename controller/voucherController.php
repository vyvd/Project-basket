<?php


class voucherController extends Controller
{

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {
        $this->table = 'vouchers';
    }

    public function saveVoucher(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
            if (!$item->id) {
                $item = ORM::for_table($this->table)->create();
            }
        } else {
            $item = ORM::for_table($this->table)->create();
        }

        $data = $input;

        $item->set($data);

        $item->save();

        return $item;

    }

}