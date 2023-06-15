<?php

class LoggerService
{

    private $name = 'logs';

    private $daily = false;

    public function addToLog(string $type, string $service, array $data = []) {
        $dir = LOGS_DIR . "/{$type}/{$service}";
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }
        $filePath = "{$dir}/{$this->name}";
        if ($this->daily) {
            $dayStr = date('Y-m-d', time());
            $filePath = "{$filePath}/{$dayStr}";
        }
        $file = fopen($filePath, "a");
        if (!$file) {
            return false;
        }

        $write = fwrite($file, $this->buildOutputString($data));
        if (!$write) {
            return false;
        }
        if (!fclose($file)) {
            return false;
        }
        return true;
    }

    private function buildOutputString(array $data) {
        $encodeData = json_encode($data);
        $date = date('Y-m-d H:i:s', time());
        return "{$date}: {$encodeData}" . PHP_EOL;
    }

    public function logException(string $type, string $service, Exception $exception) {
        return $this->addToLog($type, $service,  [
            'error_code' => $exception->getCode(),
            'error_message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param bool $daily
     */
    public function setDaily(bool $daily): void
    {
        $this->daily = $daily;
    }
}