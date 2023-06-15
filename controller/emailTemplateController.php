<?php


class emailTemplateController extends Controller
{

    /**
     * @var string
     */
    protected $table;


    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->table = 'email_templates';
    }

    public function getEmailTemplateEdit() {

        return ORM::for_table($this->table)->find_one($this->get["id"]);

    }

    public function saveTemplate($input)
    {
        if (isset($input['id'])) {  //For Update
            $item = ORM::for_table($this->table)->find_one($input['id']);
        } else {
            $item = ORM::for_table($this->table)->create();
        }
        $data = $input;

        $item->set($data);
        $item->save();

        return $item;
    }

    public function getTemplateByTitle($template)
    {
        return ORM::for_table($this->table)
            ->where('template', $template)
            ->find_one();
    }
}