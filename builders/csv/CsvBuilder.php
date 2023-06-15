<?php

class CsvBuilder
{
    const CSV_FILE_NAME = 'csv_file.csv';
    const CSV_LOCAL_PATH = __DIR__ . '/files';
    private $headings = [];
    private $data = [];
    private $file = null;
    private $redirect = false;
    private $errors = [];

    private function getFileName()
    {
        $splitOutput = explode('/', $this->file);
        $file = $splitOutput[array_key_last($splitOutput)];
        $splitFile = explode('.', $file);
        return $splitFile[array_key_first($splitFile)];
    }

    public function setHeaders()
    {
        $filename = $this->getFileName();
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$filename}.csv");
    }

    public function build()
    {
        if (!count($this->data)) {
            $this->addError([
                'message' => 'Data is empty'
            ]);
            return false;
        }
        if (!$this->file) {
            $this->addError([
                'message' => 'Output is empty'
            ]);
            return false;
        }
        if (count($this->headings)) {
            if (count($this->headings) !== count($this->data[0])) {
                $this->addError([
                    'message' => 'Headings data count mismatch'
                ]);
                return false;
            }
        }


        if ($this->redirect) {
            $output = fopen('php://output', 'w');
            $this->setHeaders();
        } else {
            if (!$this->file) {
                $filePath = self::CSV_LOCAL_PATH . '/' . self::CSV_FILE_NAME;
            } else {
                $filePath = $this->file;
            }

            if (str_contains($filePath, '/')) {
                $splitFile = explode('/', $filePath);

                unset($splitFile[array_key_last($splitFile)]);

                $directory = implode('/', $splitFile);
                if (!is_dir($directory) && !mkdir($directory, 0777)) {
                    $this->addError([
                        'message' => 'Error creating directory for csv',
                        'data' => ['directory' => $directory]
                    ]);
                    return false;
                }
            }
            $output = fopen($filePath, 'a');
        }

        if (count($this->headings)) {
            fputcsv($output, $this->headings);
        }
        foreach ($this->data as $item) {
            fputcsv($output, $item);
        }
        return true;
    }

    /**
     * @param array $headings
     */
    public function setHeadings(array $headings): self
    {
        $this->headings = $headings;
        return $this;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null $file
     */
    public function setFile($file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param bool $redirect
     */
    public function setRedirect(bool $redirect): self
    {
        $this->redirect = $redirect;
        return $this;
    }
}