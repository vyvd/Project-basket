<?php
class Console {

    const DEFAULT_ENV_FILENAME = '.newskills-env';
    const REQUIRED_OPT = 'required';
    const OPTIONAL_OPT = 'optional';

    const REQUIRED_OPTS_CONFIG = [
        ['name' => 'web_root_path', 'long_opt' => 'web_root_path', 'short_opt' => 'w', 'setting' => self::OPTIONAL_OPT],
        ['name' => 'env_file_path', 'long_opt' => 'env_file_path', 'short_opt' => 'd', 'setting' => self::OPTIONAL_OPT],
        ['name' => 'env_file_name', 'long_opt' => 'env_file_name', 'short_opt' => 'n', 'setting' => self::OPTIONAL_OPT],
        ['name' => 'command', 'long_opt' => 'command', 'short_opt' => 'c', 'setting' => self::REQUIRED_OPT],
    ];

    private $requiredOpts = [];

    private $webRootDir;
    private $envPath;
    private $envName;

    public function __construct()
    {
        define('ROOT_DIR', __DIR__ . '/../');
        define('CONSOLE_DIR', ROOT_DIR . '/console');
        define('COMMAND_DIR', CONSOLE_DIR . '/command');
        define('SERVICES_DIR', CONSOLE_DIR . '/services');
    }

    private function setConstants() {
        define('WEB_ROOT_DIR', $this->webRootDir);
        define('WEB_REPOSITORIES', WEB_ROOT_DIR . '/repositories');
        define('LOGS_DIR', CONSOLE_DIR . '/logs');

        define("DB_NAME", getenv('DB_NAME'));
        define("DB_USERNAME", getenv('DB_USERNAME'));
        define("DB_PASS", getenv('DB_PASS'));
        define("DB_HOST", getenv('DB_HOST'));

        define("SITE_URL", getenv('SITE_URL'));

        define("SITE_NAME", getenv('SITE_NAME'));

        $site_type = getenv('SITE_TYPE');
        if(empty($site_type)) {
            $site_type = 'uk';
        }
        define("SITE_TYPE", $site_type);

        $domain_name = getenv('DOMAIN_NAME');
        if(empty($domain_name)) {
            $domain_name = 'newskillsacademy.co.uk';
        }
        define("DOMAIN_NAME", $domain_name);

        define("ADMIN_EMAIL", getenv('ADMIN_EMAIL'));
    }

    private function setEnv() {
        require_once($this->webRootDir . '/vendor/autoload.php');
        $dotenv = Dotenv\Dotenv::createImmutable($this->envPath, $this->envName);
        $dotenv->load();
    }

    private function dbInit() {
        ORM::configure('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'));
        ORM::configure('username', getenv('DB_USERNAME'));
        ORM::configure('password', getenv('DB_PASS'));
        ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        ORM::configure('error_mode', PDO::ERRMODE_WARNING);
        ORM::configure('caching', true);
        ORM::configure('caching_auto_clear', true);
    }

    private static function buildOptString($setting, $value) {
        switch ($setting) {
            case self::REQUIRED_OPT;
                return "{$value}:";
            case self::OPTIONAL_OPT;
                return "{$value}::";
            default:
                return $value;
        }
    }

    public static function validateRequiredOpts(array $opt, array $requiredOptions) {
        foreach ($requiredOptions as $requiredOpt) {
            if (
                $requiredOpt['setting'] === self::REQUIRED_OPT &&
                !array_key_exists($requiredOpt['short_opt'], $opt) &&
                !array_key_exists($requiredOpt['long_opt'], $opt)) {
                Console::consoleOutput(
                    sprintf(
                        'Required option -%s or --%s is not in command',
                        $requiredOpt['short_opt'],
                        $requiredOpt['long_opt']
                    ),
                    true
                );
            }
        }
        return true;
    }

    public static function getOpts(array $opts) {
        $shortOpts = [];
        $longOpts = [];
        foreach ($opts as $opt) {
            $optSetting = null;
            if (isset($opt['setting'])) {
                $optSetting = $opt['setting'];
            }
            if (!isset($opt['short_opt']) && !isset($opt['long_opt'])) {
                Console::consoleOutput('Invalid option config', true);
            }
            if (!isset($opt['short_opt']) && isset($opt['long_opt'])) {
                $shortOpts[] = Console::buildOptString($optSetting, substr($opt['long_opt'], 0, 1));
                $longOpts[] = Console::buildOptString($optSetting, $opt['long_opt']);
                continue;
            }
            if (isset($opt['short_opt']) && isset($opt['long_opt'])) {
                $shortOpts[] = Console::buildOptString($optSetting, $opt['short_opt']);
                $longOpts[] = Console::buildOptString($optSetting, $opt['long_opt']);
            }
        }
        $shortOptsToString = implode('', $shortOpts);
        return getopt($shortOptsToString, $longOpts);
    }

    public static function buildOpts(array $opts, array $requiredOptions) {
        $optsData = [];
        foreach ($requiredOptions as $requiredOpt) {
            if (array_key_exists($requiredOpt['short_opt'], $opts)) {
                $optsData[$requiredOpt['name']] = $opts[$requiredOpt['short_opt']];
            }
            else if (array_key_exists($requiredOpt['long_opt'], $opts)) {
                $optsData[$requiredOpt['name']] = $opts[$requiredOpt['long_opt']];
            }
        }
        return $optsData;
    }

    public static function getBaseOptions() {
        $getOpts = Console::getOpts(self::REQUIRED_OPTS_CONFIG);
        if (!Console::validateRequiredOpts($getOpts, self::REQUIRED_OPTS_CONFIG)) {
            Console::consoleOutput('Options failed validation', true);
        }
        return Console::buildOpts($getOpts, self::REQUIRED_OPTS_CONFIG);
    }

    public function init() {
        $this->requiredOpts = self::getBaseOptions();

        if (isset($this->requiredOpts['web_root_path']) && $this->requiredOpts['web_root_path'] !== '') {
            $this->webRootDir = $this->requiredOpts['web_root_path'];
        } else {
            $this->webRootDir = ROOT_DIR;
        }
        if (isset($this->requiredOpts['env_file_path']) && $this->requiredOpts['env_file_path'] !== '') {
            $this->envPath = $this->requiredOpts['env_file_path'];
        } else {
            $this->envPath = ROOT_DIR;
        }
        if (isset($this->requiredOpts['env_file_name']) && $this->requiredOpts['env_file_name'] !== '') {
            $this->envName = $this->requiredOpts['env_file_name'];
        } else {
            $this->envName = self::DEFAULT_ENV_FILENAME;
        }
        $this->setEnv();
        $this->setConstants();
        $this->dbInit();

        $commandClassName = "{$this->requiredOpts['command']}Command";
        $filePath = COMMAND_DIR . "/{$commandClassName}.php";

        if (!file_exists($filePath)) {
            self::consoleOutput('Error finding file', true);
        }
        require_once(COMMAND_DIR . '/BaseCommand.php');
        require_once($filePath);

        if(!class_exists($commandClassName)) {
            self::consoleOutput('Error finding class', true);
        }

        $commandClassInstance = new $commandClassName();
        if(!method_exists($commandClassInstance, 'run')) {
            self::consoleOutput('Error finding class init method', true);
        }
        if(get_parent_class($commandClassInstance) !== BaseCommand::class) {
            self::consoleOutput("Error, Command class {$commandClassName} does not extend BaseCommand", true);
        }
        $commandClassInstance->run();
    }

    public static function consoleOutput(string $message, ?bool $throwException = false) {
        echo $message . PHP_EOL;
        if ($throwException) {
            exit(0);
        }
    }
    public static function consoleOutputData(array $data, ?bool $throwException = false) {
        echo json_encode($data) . PHP_EOL;
        if ($throwException) {
            exit(0);
        }
    }
}

?>
