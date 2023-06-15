<?php
require_once(COMMAND_DIR . '/BaseCommand.php');
require_once(SERVICES_DIR . '/EmailService.php');
require_once(WEB_REPOSITORIES . '/AccountsRepository.php');
require_once(WEB_REPOSITORIES . '/CoursesAssignedRepository.php');

class MonthlyLearningReportEmailCommand extends BaseCommand
{
    const LOGGER_NAME = 'monthly-learning-report';
    private $monthlyLearningReportBuilder;
    private $accountsRepository;

    private $emailOverride = false;
    private $emails = [];
    private $offset = 0;
    private $batchLimit = 1000;
    private $limit = null;
    private $batchInterval = 60;
    private $interval = 0;

    private $timePeriod = '-12 months';

    public function __construct()
    {
        parent::__construct();
        $builderPath = WEB_ROOT_DIR . '/builders/emails/MonthlyLearningReportBuilder.php';
        if (!file_exists($builderPath)) {
            Console::consoleOutput('Error finding builder file', true);
        }
        require_once($builderPath);
        $this->accountsRepository = new AccountsRepository();
        $this->monthlyLearningReportBuilder = new MonthlyLearningReportBuilder();
        $this->optionsInit([
            ['name' => 'time_period', 'long_opt' => 'time_period', 'short_opt' => 't', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'limit', 'long_opt' => 'limit', 'short_opt' => 'l', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'batch_limit', 'long_opt' => 'batch_limit', 'short_opt' => 'b', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'interval', 'long_opt' => 'interval', 'short_opt' => 'i', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'batch_interval', 'long_opt' => 'batch_interval', 'short_opt' => 'j', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'offset', 'long_opt' => 'offset', 'short_opt' => 'o', 'setting' => Console::OPTIONAL_OPT],
            ['name' => 'email_override', 'long_opt' => 'email_override', 'short_opt' => 'e', 'setting' => Console::OPTIONAL_OPT]
        ]);
    }

    public function run()
    {
        try {
            $accountTable = $this->accountsRepository->getTableName();
            $this->init();
            if ($this->emailOverride) {
                $fetchAccounts = ORM::for_table($accountTable)
                    ->select('id')
                    ->select('firstname')
                    ->select('lastname')
                    ->select('email')
                    ->whereIn('email', $this->emails)->findMany();
            } else {
                $courseAssignedTable = (new CoursesAssignedRepository())->getTableName();
                $this->accountsRepository->setResultFormat(BaseRepository::RESULT_FORMAT_RAW)
                    ->setSelect([
                        "{$accountTable}.id",
                        "{$accountTable}.firstname",
                        "{$accountTable}.lastname",
                        "{$accountTable}.email",
                        "{$courseAssignedTable}.whenAssigned as whenAssigned"])
                    ->setLimit($this->limit)
                    ->setOffset($this->offset);
                $fetchAccounts = $this->accountsRepository->fetchAssignedCourses(
                    date('Y-m-d H:i:s', strtotime($this->timePeriod)),
                    date('Y-m-d H:i:s', time())
                );
            }
            $accountsChunks = array_chunk($fetchAccounts, $this->batchLimit);
            $batchCount = count($accountsChunks);
            foreach ($accountsChunks as $index => $accountsChunk) {
                foreach ($accountsChunk as $account) {
                    try {
                        if (!$account->get('id')) {
                            $this->loggerService->addToLog('email', self::LOGGER_NAME, [
                                'message' => 'Invalid account',
                                'account' => $account->as_array()
                            ]);
                        }
                        $this->sendEmails($account);
                    } catch (Exception $exception) {
                        $this->loggerService->logException('email', self::LOGGER_NAME, $exception);
                        Console::consoleOutput($exception->getMessage());
                    }
                    sleep($this->interval);
                }
                $batchNum = $index + 1;
                if (isset($accountsChunks[$batchNum])) {
                    Console::consoleOutput(
                        "Finished batch No. {$batchNum} out of {$batchCount}, Sleeping for {$this->batchInterval} seconds"
                    );
                    sleep($this->batchInterval);
                }
            }
        } catch (Exception $exception) {
            Console::consoleOutput($exception->getMessage(), true);
            $this->loggerService->logException('email', self::LOGGER_NAME, $exception);
        }
        $this->onFinish();
    }

    private function sendEmails(ORM $account)
    {
        $emailFrom = getenv('MLR_EMAIL_FROM') ?? false;
        $emailSubject = getenv('MLR_EMAIL_SUBJECT') ?? 'Monthly Learning Report';
        $this->monthlyLearningReportBuilder->setAccount($account);
        $content = $this->monthlyLearningReportBuilder->renderReportHtml();
        $emailService = new EmailService();
        $emailService->setMessage($content);
        $results = $emailService->sendEmail(
            [$account->get('email')],
            $emailSubject,
            $emailFrom
        );
        $this->loggerService->addToLog('email', self::LOGGER_NAME, array_merge(
            $results,
            $account->as_array()
        ));
    }

    private function init()
    {
        $options = $this->getOptions();
        if (isset($options['limit'])) {
            if (
                !$options['limit'] ||
                $options['limit'] === -1 ||
                $options['limit'] === '-1' ||
                $options['limit'] === 'false'
            ) {
                $this->limit = null;
            } else {
                $this->limit = (int)$options['limit'];
            }
        }
        if (isset($options['batch_limit'])) {
            $this->batchLimit = (int)$options['batch_limit'];
        }
        if (isset($options['interval'])) {
            $this->interval = (int)$options['interval'];
        }
        if (isset($options['batch_interval'])) {
            $this->batchInterval = (int)$options['batch_interval'];
        }
        if (isset($options['offset'])) {
            $this->offset = (int)$options['offset'];
        }
        if (isset($options['time_period'])) {
            $this->timePeriod = $options['time_period'];
        }
        if (isset($options['email_override'])) {
            $emails = trim($options['email_override']);
            $this->emailOverride = true;
            $splitEmails = explode(',', trim($emails));
            $this->emails = $splitEmails;
        }
    }

    private function onFinish()
    {
        Console::consoleOutput('Finished...');
        exit(0);
    }
}