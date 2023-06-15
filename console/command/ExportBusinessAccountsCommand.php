<?php
require_once(COMMAND_DIR . '/BaseCommand.php');
require_once(ROOT_DIR . '/helpers/AccountsHelpers.php');
require_once(SERVICES_DIR . '/EmailService.php');

class ExportBusinessAccountsCommand extends BaseCommand
{
    const LOGGER_NAME = 'export-business-accounts';
    const CSV_NAME = 'export-business-accounts';

    public function __construct()
    {
        parent::__construct();

        $this->optionsInit([
            ['name' => 'emails', 'long_opt' => 'emails', 'short_opt' => 'e', 'setting' => Console::REQUIRED_OPT],
        ]);
    }

    private function buildCsvPath(string $csvName) {
        $path = CONSOLE_DIR . '/csv/' . $csvName . '.csv';
        if (!file_exists($path)) {
            return $path;
        }
        $csvName .= '-1';
        return $this->buildCsvPath($csvName);
    }

    public function run()
    {
        try {
            $options = $this->getOptions();
            if (!isset($options['emails'])) {
                Console::consoleOutput('Error building csv', true);
                $this->loggerService->addToLog('email', self::LOGGER_NAME, [
                    'message' => 'Error email not set',
                ]);
            }
            $emails = trim($options['emails']);
            $toEmails = explode(',', trim($emails));

            $path = $this->buildCsvPath(self::CSV_NAME);

            $buildCsv = AccountsHelpers::exportAccountsBusinessEmails(
                $path
            );
            if (!$buildCsv) {
                Console::consoleOutput('Error building csv', true);
                $this->loggerService->addToLog('email', self::LOGGER_NAME, [
                    'message' => 'Error building csv',
                    'path' => $path
                ]);
            }
            $emailService = new EmailService();
            $emailService->setHeaders(['Content-Type: multipart/form-data']);
            $emailService->addAttachment('attachment', $path, 'text/csv', self::CSV_NAME);
            $emailService->setMessage('Your business accounts export is attached to this email.');
            $sendEmail = $emailService->sendEmail(
                $toEmails,
                'Business Accounts Csv Export'
            );
            $this->loggerService->addToLog('csv', self::LOGGER_NAME, [
                'message' => 'Success',
                'emails' => $toEmails,
                'data' => $sendEmail
            ]);

            //Delete csv file
            unlink($path);
            Console::consoleOutputData([
                'message' => 'Success',
                'emails' => $toEmails,
                'data' => $sendEmail
            ]);
            Console::consoleOutput('Finished...');
            exit(0);
        } catch (Exception $exception) {
            Console::consoleOutput($exception->getMessage(), true);
            $this->loggerService->logException('email', self::LOGGER_NAME, $exception);
        }
    }
}