<?php

class DbConnectionHandler
{
    const LOCAL_DB_CONNECTION = 'current-website';
    const CONNECTION = 'CONNECTION';

    const SITE_URL = 'SITE_URL';
    const DB_HOST = 'DB_HOST';
    const DB_NAME = 'DB_NAME';
    const DB_USERNAME = 'DB_USERNAME';
    const DB_PASSWORD = 'DB_PASSWORD';

    private $errors = [];

    public function handleBatchConnections(array $connections)
    {
        foreach ($connections as $connection) {
            $this->handleConnection($connection);
        }
    }

    private function getCourseConnectionKeys() {
        return array_map(function ($item) {
            return $item['key'];
        }, COURSE_COPY_CONNECTIONS);
    }
    public static function getCourseConnectionByKey($key) {
        $findCourseConnectionIndex = array_search($key, array_column(COURSE_COPY_CONNECTIONS, 'key'));
        if ($findCourseConnectionIndex === false) {
            return false;
        }
        return COURSE_COPY_CONNECTIONS[$findCourseConnectionIndex];
    }

    public function handleConnection(?string $connection = null)
    {
        if ($connection === self::LOCAL_DB_CONNECTION) {
            return $this->buildConnectionData(self::LOCAL_DB_CONNECTION, SITE_URL);
        }
        if (!in_array($connection, $this->getCourseConnectionKeys())) {
            $this->addError('Db connection does not exist', ['connection' => $connection]);
            return false;
        }
        return $this->createConnection($connection);
    }

    private function getConnectionEnvPrefix(string $connection) {
        return strtoupper($connection);
    }

    private function getEnvName($prefix, $appendix) {
        return sprintf('%s_%s', $prefix, $appendix);
    }

    private function buildCredentials(string $connection)
    {
        $connectionEnvPrefix = $this->getConnectionEnvPrefix($connection);
        return [
            self::SITE_URL => getenv($this->getEnvName($connectionEnvPrefix, self::SITE_URL)),
            self::DB_HOST => getenv($this->getEnvName($connectionEnvPrefix, self::DB_HOST)),
            self::DB_NAME => getenv($this->getEnvName($connectionEnvPrefix, self::DB_NAME)),
            self::DB_USERNAME => getenv($this->getEnvName($connectionEnvPrefix, self::DB_USERNAME)),
            self::DB_PASSWORD => getenv($this->getEnvName($connectionEnvPrefix, self::DB_PASSWORD)),
        ];

    }

    private function validateConnection(string $connection)
    {
        $config = $this->buildCredentials($connection);

        $connectionEnvPrefix = $this->getConnectionEnvPrefix($connection);
        if (empty($config[self::SITE_URL])) {
            $this->addError("Env {$this->getEnvName($connectionEnvPrefix, self::SITE_URL)} is invalid");
            return false;
        }
        if (empty($config[self::DB_HOST])) {
            $this->addError("Env {$this->getEnvName($connectionEnvPrefix, self::DB_HOST)} is invalid");
            return false;
        }
        if (empty($config[self::DB_NAME])) {
            $this->addError("Env {$this->getEnvName($connectionEnvPrefix, self::DB_NAME)} is invalid");
            return false;
        }
        if (empty($config[self::DB_USERNAME])) {
            $this->addError("Env {$this->getEnvName($connectionEnvPrefix, self::DB_USERNAME)} is invalid");
            return false;
        }

        if (empty($config[self::DB_PASSWORD])) {
            $this->addError("Env {$this->getEnvName($connectionEnvPrefix, self::DB_PASSWORD)} is invalid");
            return false;
        }
        return true;
    }

    public function createConnection(string $connection)
    {
        if (!$this->validateConnection($connection)) {
            return false;
        }
        $config = $this->buildCredentials($connection);
        if (!$config) {
            return false;
        }
        ORM::configure("mysql:host={$config[self::DB_HOST]};dbname={$config[self::DB_NAME]}", null, $connection);
        ORM::configure('username', $config[self::DB_USERNAME], $connection);
        ORM::configure('password', $config[self::DB_PASSWORD], $connection);
        return $this->buildConnectionData($connection, $config[self::SITE_URL]);
    }

    private function buildConnectionData($connection, $url)
    {
        return [
            self::CONNECTION => $connection,
            self::SITE_URL => $url
        ];
    }

    public function addError($message, ?array $error = []): void
    {
        $buildError = [
            'message' => $message,
        ];
        if (count($error)) {
            $buildError['data'] = $error;
        }
        $this->errors[] = $buildError;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    public static function getInstance() {
        return new self();
    }
}