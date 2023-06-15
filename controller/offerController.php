<?php


class offerController extends Controller
{

    /**
     * @var string
     */
    protected $table;

    public function __construct()
    {
        $this->table = 'offerPages';
    }

    public function getOfferByID($id)
    {
        return ORM::for_table($this->table)->find_one($id);
    }

    public function getOfferByOldID($oldId, $usImport = '0')
    {
        return ORM::for_table($this->table)
            ->where('oldId', $oldId)
            ->where('usImport', $usImport)
            ->find_one();
    }

    public function saveOffer(array $input)
    {

        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
        } else { //For Create
            $input["whenAdded"] = isset($input["whenAdded"]) ? $input["whenAdded"] : date("Y-m-d H:i:s");
            $item = ORM::for_table($this->table)->create();
        }

        $data = $input;

        $data['maxCourses'] = isset($data["maxCourses"]) ? $data["maxCourses"] : 1;
        //$data['whenUpdated'] = isset($data["whenUpdated"]) ? $data["whenUpdated"] : date("Y-m-d H:i:s");

        $data['courses'] = @$data['courses'] ? json_encode($data['courses']) : null;


        $item->set($data);
        $item->save();
        // redirect to edit course so modules, etc can be added
        return $item;
    }


}