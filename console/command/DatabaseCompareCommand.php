<?php
require_once(COMMAND_DIR . '/BaseCommand.php');
require_once(ROOT_DIR . '/helpers/AccountsHelpers.php');
require_once(SERVICES_DIR . '/EmailService.php');

class DatabaseCompareCommand extends BaseCommand
{
    const LOGGER_NAME = 'export-business-accounts';
    const CSV_NAME = 'export-business-accounts';

    public function __construct()
    {
        parent::__construct();

        $this->optionsInit([
            ['name' => 'db_host', 'long_opt' => 'db_host', 'short_opt' => 'h', 'setting' => Console::REQUIRED_OPT],
            ['name' => 'db_name', 'long_opt' => 'db_name', 'short_opt' => 'b', 'setting' => Console::REQUIRED_OPT],
            ['name' => 'db_username', 'long_opt' => 'db_username', 'short_opt' => 'u', 'setting' => Console::REQUIRED_OPT],
            ['name' => 'db_password', 'long_opt' => 'db_password', 'short_opt' => 'p', 'setting' => Console::REQUIRED_OPT],
        ]);
    }


    public function run()
    {
        try {
            require_once(ROOT_DIR . '/repositories/AccountsRepository.php');
            $baseRepo = new AccountsRepository();

            $options = $this->getOptions();
            ORM::configure("mysql:host={$options['db_host']};dbname={$options['db_name']}", null, $options['db_host']);
            ORM::configure('username', $options['db_username'], $options['db_host']);
            ORM::configure('password', $options['db_password'], $options['db_host']);

            $compareSiteTableCols = $baseRepo->compareDbs($options['db_host'], $options, false);
            Console::consoleOutput($compareSiteTableCols);
            exit(0);
        } catch (Exception $exception) {
            Console::consoleOutput($exception->getMessage(), true);
        }
    }
}