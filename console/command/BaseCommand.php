<?php

require_once(SERVICES_DIR . '/LoggerService.php');
class BaseCommand
{
    protected $loggerService;

    private $options = [];

    public function __construct()
    {
        $this->loggerService = new LoggerService();
    }


    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
    /**
     * @param array $options
     */
    public function optionsInit(array $options): void
    {
        $baseOpts = Console::getBaseOptions();

        $getOpts = Console::getOpts(array_merge($options, Console::REQUIRED_OPTS_CONFIG));
        if (!Console::validateRequiredOpts($getOpts, $options)) {
            Console::consoleOutput('Class options validation failed', true);
        }
        $getOpts = Console::buildOpts($getOpts, $options);
        $this->options = array_merge($baseOpts, $getOpts);
    }

}