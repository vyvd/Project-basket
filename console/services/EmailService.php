<?php

class EmailService
{
    const ERROR_SEND_RESPONSE_MESSAGE = 'Error sending email';
    const SUCCESS_SEND_RESPONSE_MESSAGE = 'Email sent';

    private $apiUser;
    private $apiPassword;
    private $sendMessageEndpoint;

    private $headers = [];

    private $attachments = [];

    private $message = null;

    public function __construct()
    {
        $this->setApiUser(getenv('MAILGUN_API_USER'));
        $this->setApiPassword(getenv('MAILGUN_API_PASSWORD'));
        $this->setSendMessageEndpoint(getenv('MAILGUN_API_MESSAGES'));
    }

    public function sendEmail(array $to = [], string $subject = null, string $from = 'New Skills Academy <noreply@newskillsacademy.co.uk>')
    {
        $array_data = array(
            'to' => implode(',', $to),
            'from' => $from,
            'subject' => $subject,
            'html' => $this->message,
            'text' => $subject,
            'o:tracking' => 'yes',
            'o:tracking-clicks' => 'yes',
            'o:tracking-opens' => 'yes',
            'o:tag' => "test",
            'h:Reply-To' => $from
        );

        foreach ($this->attachments as $attachment) {
            if (!isset($attachment['key'])) {
                continue;
            }
            if (!isset($attachment['file'])) {
                continue;
            }
            if (!$attachment['file'] instanceof CURLFile) {
                continue;
            }
            $array_data[$attachment['key']] = $attachment['file'];
        }

        // key-6a1e1b7e69593811eed4fde8fac7372d
        // new private key: key-c2a1090b9f1847f43ab40ea6073b9566

        $session = curl_init($this->sendMessageEndpoint);
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($session, CURLOPT_USERPWD, 'api:key-6a1e1b7e69593811eed4fde8fac7372d'); // key-6a1e1b7e69593811eed4fde8fac7372d
        curl_setopt($session, CURLOPT_USERPWD, "{$this->apiUser}:{$this->apiPassword}"); // key-c2a1090b9f1847f43ab40ea6073b9566
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        if (count($this->headers)) {
            curl_setopt($session, CURLOPT_HTTPHEADER, $this->headers);
        }
        $response = curl_exec($session);
        $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
        curl_close($session);
        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
            return [
                'status' => 'success',
                'message' => (is_array($responseData) && isset($responseData['message']))
                    ?
                    $responseData['message']
                    :
                    self::SUCCESS_SEND_RESPONSE_MESSAGE,
                'data' => $responseData
            ];
        }

        return [
            'status' => 'error',
            'message' => (is_string($response))? $response : self::ERROR_SEND_RESPONSE_MESSAGE,
            'data' => $response
        ];
    }

    /**
     * @param string $apiUser
     */
    public function setApiUser(string $apiUser): void
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @param string $apiPassword
     */
    public function setApiPassword(string $apiPassword): void
    {
        $this->apiPassword = $apiPassword;
    }

    /**
     * @param string $sendMessageEndpoint
     */
    public function setSendMessageEndpoint(string $sendMessageEndpoint): void
    {
        $this->sendMessageEndpoint = $sendMessageEndpoint;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @param array $attachments
     */
    public function addAttachment(string $key, string $filename, ?string $mime_type = null, ?string $posted_filename = null): void
    {
        $this->attachments[] = [
            'key' => $key,
            'file' => new CURLFile($filename, $mime_type, $posted_filename)
        ];
    }

    /**
     * @param null $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

}