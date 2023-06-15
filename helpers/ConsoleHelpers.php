<?php

class ConsoleHelpers
{
    const CONSOLE_PATH = __DIR__ . '/../console.php';
    private $command;
    private $webFilesPath;
    private $envFileDirPath;
    private $envFileName;

    private $arguments = [];

    public function runConsoleCommand()
    {
        if (!$this->command) {
            return false;
        }
        if (!$this->webFilesPath) {
            return false;
        }
        if (!$this->envFileDirPath) {
            return false;
        }
        if (!$this->envFileName) {
            return false;
        }
        $command = sprintf(
            'php %s -c=%s -w="%s" -d="%s" -n="%s" %s > /dev/null 2>&1 & echo $!',
            self::CONSOLE_PATH,
            $this->command,
            $this->webFilesPath,
            $this->envFileDirPath,
            $this->envFileName,
            (count($this->arguments)) ? implode(' ', $this->arguments) : ''
        );
        $runCommand = shell_exec($command);
        if (is_string($runCommand)) {
            return trim($runCommand);
        }
        return $runCommand;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command): void
    {
        $this->command = $command;
    }

    /**
     * @param mixed $webFilesPath
     */
    public function setWebFilesPath($webFilesPath): void
    {
        $this->webFilesPath = $webFilesPath;
    }

    /**
     * @param mixed $envFileDirPath
     */
    public function setEnvFileDirPath($envFileDirPath): void
    {
        $this->envFileDirPath = $envFileDirPath;
    }

    /**
     * @param mixed $envFileName
     */
    public function setEnvFileName($envFileName): void
    {
        $this->envFileName = $envFileName;
    }

    /**
     * @param mixed $arguments
     */
    public function setArguments($arguments): void
    {
        $this->arguments = $arguments;
    }


    public function addArgument(string $name, $value): void
    {
        $argument = "{$name}";
        if (is_int($value) || is_bool($value)) {
            $argument .= "={$value}";
        } elseif (is_array($value)) {
            $toString = implode(',', $value);
            $argument .= "=\"{$toString}\"";
        } else {
            $argument .= "=\"{$value}\"";
        }
        $this->arguments[] = $argument;
    }


}