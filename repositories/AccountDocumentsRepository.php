<?php

require_once('BaseRepository.php');

class AccountDocumentsRepository extends BaseRepository
{
    protected $tableName = "accountDocuments";

    public function __construct()
    {
        parent::__construct();
    }
}