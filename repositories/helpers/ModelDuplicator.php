<?php

require_once(__DIR__ . '/RepositoryHelpers.php');
require_once(__DIR__ . '/DbConnectionHandler.php');

class ModelDuplicator
{

    private $dbConnections = [];

    private $sourceRepository;
    private $sourceModelData;
    private $relatedTables;
    private $sourceTableDataCallback;
    private $relatedTableDataCallback;

    private $dbConnectionHandler;

    public function __construct()
    {
        $this->dbConnectionHandler = new DbConnectionHandler();
    }

    public static function buildConnections($copyCourseDest)
    {
        $connections = [];
        if (isset($copyCourseDest) && $copyCourseDest !== '') {
            $connections = explode(',', $copyCourseDest);
        }
        return $connections;
    }

    public static function connectionsIsLocalOnly($connections): bool
    {
        return (!count($connections) || (count($connections) === 1 && array_search(
                    DbConnectionHandler::LOCAL_DB_CONNECTION,
                    $connections
                ) > -1));
    }

    public function processModelDuplicatorDataBeforeSave(array $data)
    {
        if (array_key_exists('id', $data)) {
            unset($data['id']);
        }
        if (array_key_exists('oldID', $data)) {
            unset($data['oldID']);
        }
        if (array_key_exists('whenAdded', $data)) {
            $data['whenAdded'] = BaseRepository::getDateTime();
        }
        if (array_key_exists('whenUpdated', $data)) {
            $data['whenUpdated'] = BaseRepository::getDateTime();
        }
        return $data;
    }

    public function processModelDuplicatorModelBeforeSave(ORM $model, array $data)
    {
        return $model;
    }

    /**
     * Duplicates/Clones a table, as well as it's related tables
     * $ignoredTables - An array of table names that will be ignored from duplication
     *
     * @param $repository
     * @param $idValue
     * @param array $relatedColumns
     * @param array $ignoredTables
     * @param bool $sourceTableDataCallback
     * @param bool $relatedTableDataCallback
     * @return false|ORM|array
     */
    public function modelDuplicator(
        $repository,
        $idValue,
        array $relatedColumns = [],
        array $ignoredTables = [],
        $sourceTableDataCallback = false,
        $relatedTableDataCallback = false
    ) {
        //Class has to extend BaseRepository
        if (get_parent_class($repository) !== BaseRepository::class) {
            return false;
        }
        $this->sourceTableDataCallback = $sourceTableDataCallback;
        $this->relatedTableDataCallback = $relatedTableDataCallback;

        $this->sourceRepository = $repository;

        //Fetches all tables that have columns specified in $relatedColumns
        $fetchTablesWithCourseRel = $this->sourceRepository->fetchTablesByColumn($relatedColumns);

        //Add error if related tables fetch fails
        if (!is_array($fetchTablesWithCourseRel)) {
            RepositoryHelpers::errorLog([
                'class' => __CLASS__,
                'function' => __FUNCTION__,
                'message' => 'Error fetching table columns',
                'data' => $relatedColumns
            ]);

            //Set related tables to empty array so only the source table will be cloned
            $fetchTablesWithCourseRel = [];
        }

        //Removed the ignored tables
        $this->relatedTables = array_filter($fetchTablesWithCourseRel, function ($column, $table) use ($ignoredTables) {
            return !in_array($table, $ignoredTables);
        }, ARRAY_FILTER_USE_BOTH);

        //Fetch the source row by id
        $this->sourceModelData = $this->sourceRepository->fetchByParamsQuery([], ['id' => $idValue])->findOne(
        )->asArray();

        if (!is_array($this->sourceModelData) || !count($this->sourceModelData)) {
            RepositoryHelpers::errorLog([
                'class' => __CLASS__,
                'function' => __FUNCTION__,
                'message' => 'Error fetching model by id',
                'data' => $relatedColumns
            ]);
            return false;
        }
        $results = [];

        foreach ($this->dbConnections as $connection) {
            try {
                $getConnection = $this->dbConnectionHandler->handleConnection($connection);
                if (!$getConnection) {
                    RepositoryHelpers::errorLog([
                        'class' => __CLASS__,
                        'function' => __FUNCTION__,
                        'message' => 'Model db connection error',
                        'data' => $this->dbConnectionHandler->getErrors()
                    ]);
                    continue;
                }
            } catch (Exception $exception) {
                $this->logException($exception);
                continue;
            }
            $results[] = array_merge(
                [
                    'model' => $this->createDuplications($connection)
                ],
                $getConnection
            );
        }
        return $results;
    }

    private function createDuplications($dbConnection = DbConnectionHandler::LOCAL_DB_CONNECTION)
    {
        if (count($this->dbConnections) && empty($dbConnection)) {
            return false;
        }

        try {
            if ($dbConnection === DbConnectionHandler::LOCAL_DB_CONNECTION) {
                $createModelOrm = ORM::for_table($this->sourceRepository->getTableName());
            } else {
                $createModelOrm = ORM::for_table($this->sourceRepository->getTableName(), $dbConnection);
            }
        } catch (Exception $exception) {
            RepositoryHelpers::errorLog([
                'class' => __CLASS__,
                'function' => __FUNCTION__,
                'message' => $exception->getMessage(),
            ]);
            return false;
        }
        $createModel = $createModelOrm->create();
        $sourceModelDataClone = $this->sourceModelData;
        //Copy source  id
        $oldId = $sourceModelDataClone['id'];

        //Process the clone  data before its saved
        $sourceModelDataClone = $this->processModelDuplicatorDataBeforeSave($sourceModelDataClone);
        //Process the clone  ORM instance before its saved
        $createModel = $this->processModelDuplicatorModelBeforeSave($createModel, $sourceModelDataClone);

        //If a callback is passed, call it
        if ($this->sourceTableDataCallback !== false) {
            require_once(__DIR__ . '/../../helpers/UtilHelpers.php');
            $sourceModelDataClone = UtilHelpers::callbackHandler(
                $this->sourceTableDataCallback,
                $this->sourceRepository->getTableName(),
                $sourceModelDataClone,
                $dbConnection
            );
        }

        $createModel->set($sourceModelDataClone);
        if (!$createModel->save()) {
            RepositoryHelpers::errorLog([
                'class' => __CLASS__,
                'function' => __FUNCTION__,
                'message' => 'Error saving source Model/Table',
                'data' => [
                    'table' => $this->sourceRepository->getTableName(),
                    'id' => ($sourceModelDataClone instanceof ORM) ? $sourceModelDataClone->get('id') : false
                ]
            ]);
            return false;
        }

        //Set new cloned
        $newId = $createModel->get('id');
        RepositoryHelpers::recordLog(
            "Copied course: {$sourceModelDataClone['title']} to {$sourceModelDataClone['title']}"
        );

        //Iterate through the related tables and clone them
        foreach ($this->relatedTables as $table => $column) {
            //Fetch related table to clone by the related/foreign key column

            try {
                $fetchTable = ORM::for_table($table)
                    ->where($column, (int)$oldId)
                    ->findMany();
            } catch (Exception $exception) {
                $this->logException($exception);
                return false;
            }
            //Iterate results and save new rows
            foreach ($fetchTable as $tableItem) {
                $tableData = $tableItem->asArray();
                try {
                    if ($dbConnection === DbConnectionHandler::LOCAL_DB_CONNECTION) {
                        $tableOrm = ORM::for_table($table);
                    } else {
                        $tableOrm = ORM::for_table($table, $dbConnection);
                    }
                } catch (Exception $exception) {
                    $this->logException($exception);
                    return false;
                }
                $createTable = $tableOrm->create();
                $tableData[$column] = $newId;

                $tableData = $this->processModelDuplicatorDataBeforeSave($tableData);
                $createTable = $this->processModelDuplicatorModelBeforeSave($createTable, $tableData);

                //If a callback is passed, call it
                if ($this->relatedTableDataCallback !== false) {
                    require_once(__DIR__ . '/../../helpers/UtilHelpers.php');
                    $tableData = UtilHelpers::callbackHandler(
                        $this->relatedTableDataCallback,
                        $table,
                        $tableData,
                        $dbConnection
                    );
                }

                $createTable->set($tableData);
                if (!$createTable->save()) {
                    RepositoryHelpers::errorLog([
                        'class' => __CLASS__,
                        'function' => __FUNCTION__,
                        'message' => 'Error saving related table',
                        'data' => $tableData
                    ]);
                    continue;
                }
                RepositoryHelpers::recordLog("Copied {$table} id: {$tableItem['id']} to id:{$createTable->id}");
            }
        }
        return $createModel;
    }

    /**
     * @param array $dbConnections
     */
    public function setDbConnections(array $dbConnections): void
    {
        $this->dbConnections = $dbConnections;
    }

    /**
     * @return DbConnectionHandler
     */
    public function getDbConnectionHandler(): DbConnectionHandler
    {
        return $this->dbConnectionHandler;
    }

    private function logException(Exception $exception)
    {
        RepositoryHelpers::errorLog([
            'class' => __CLASS__,
            'function' => __FUNCTION__,
            'message' => $exception->getMessage(),
        ]);
    }
}