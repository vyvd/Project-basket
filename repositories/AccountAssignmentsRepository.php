<?php

require_once('BaseRepository.php');

class AccountAssignmentsRepository extends BaseRepository
{
    protected $tableName = "accountAssignments";

    public function __construct()
    {
        parent::__construct();
    }
}