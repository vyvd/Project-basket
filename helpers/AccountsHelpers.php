<?php

class AccountsHelpers
{

    /**
     * Exports accounts with non-common emails to a downloadable csv
     *
     * @return bool
     */
    public static function exportAccountsBusinessEmails(?string $filePath = null, ?int $batchLimit = 1000, ?int $limit = null)
    {
        require_once(__DIR__ . '/../repositories/AccountsRepository.php');
        require_once(__DIR__ . '/../builders/csv/CsvBuilder.php');

        $accountsRepo = new AccountsRepository();

        //Initialise CsvBuilder class
        $csvBuilder = new CsvBuilder();

        //Csv header fields
        $fields = [
            "id",
            "firstname",
            "lastname",
            "email",
            "whenCreated",
            "CoursePurchased",
        ];
        $offset = 0;
        $step = 0;
        $finished = false;
        $resultTotalCount = 0;
        while (!$finished) {
            if ($limit && is_int($limit) && $resultTotalCount > $limit) {
                $finished = true;
                continue;
            }
            //Fetch accounts with non-common email addresses with courses relations
            $results = $accountsRepo->fetchBusinessAccounts($batchLimit, $offset);

            $resultCount = count($results);
            $resultTotalCount = $resultTotalCount + $resultCount;

            if (!$resultCount) {
                $finished = true;
                continue;
            }
            $offset = $offset + $batchLimit + 1;

            //Build the csv data/rows using the fields values to get the correct account values
            $data = array_map(function ($account) use ($fields) {
                return array_map(function ($field) use ($account) {
                    if ($field === 'CoursePurchased') {
                        $field = 'title';
                    }
                    if (isset($account[$field])) {
                        return $account[$field];
                    }
                    return false;
                }, $fields);
            }, $results);

            //Remove any invalid values from the data
            $filterData = array_filter($data, function ($item) {
                return $item;
            }, ARRAY_FILTER_USE_BOTH);
            if ($step === 0) {
                $csvBuilder->setHeadings($fields);
            } else {
                $csvBuilder->setHeadings([]);
            }
            //Set the name of the csv file and build csv
            $buildCsv = $csvBuilder->setFile($filePath)
                ->setData($filterData)
                ->setRedirect(false)
                ->build();
            if ($buildCsv) {
                $step++;
                continue;
            }
            //Set csv with error message if it fails to build
            $csvBuilder->setHeadings([])
                ->setData([['Error building business accounts spreadsheet.']])
                ->build();
            self::addToErrorLog($csvBuilder->getErrors());
            return false;
        }
        return true;
    }

    public static function addToErrorLog(array $data) {
        $date = date('Y-m-d H:i:s', time());
        $errorString = json_encode($data);
        error_log("{$date}: {$errorString}");
    }
}