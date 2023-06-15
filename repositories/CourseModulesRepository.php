<?php

require_once('BaseRepository.php');
require_once('CoursesRepository.php');

class CourseModulesRepository extends BaseRepository
{
    protected $tableName = "courseModules";

    public function __construct()
    {
        parent::__construct();
    }

    public function modulesModelDuplicator($courseModuleId, ?array $dbConnections = []) {

        require_once(APP_ROOT_PATH . 'repositories/helpers/ModelDuplicator.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseModulesRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/AccountAssignmentsRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CoursesAssignedRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseModuleProgressRepository.php');
        require_once(APP_ROOT_PATH . 'repositories/CourseNotesRepository.php');

        $ignoreTables = [
            (new AccountAssignmentsRepository())->getTableName(),
            (new CoursesAssignedRepository())->getTableName(),
            (new CourseModuleProgressRepository())->getTableName(),
            (new CourseNotesRepository())->getTableName(),
        ];

        $courseModulesRepo = new CourseModulesRepository();
        $modelDuplicator = new ModelDuplicator();
        $modelDuplicator->setDbConnections($dbConnections);
        return $modelDuplicator->modelDuplicator(
            $courseModulesRepo,
            $courseModuleId,
            ['moduleID', 'module_id'],
            $ignoreTables,
            function ($tableName, $modelData, $dbConnection) {
                $modelData['title'] = "{$modelData['title']} Copy";
                $modelData['slug'] = "{$modelData['slug']}_copy";
                return $modelData;
            }
        );
    }
}