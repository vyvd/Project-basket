<?php

use Aws\S3\S3Client;

class AWSService
{
    public static function setupS3Client() {
        $s3 = new S3Client([
            'region' => 'us-west-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => AWS_KEY,
                'secret' => AWS_SECRET_KEY,
            ]
        ]);

        return $s3;
    }

    /**
     * @param $file
     * @param $fileName
     * @return \Aws\Result
     */
    public static function putToS3($file, $fileName): \Aws\Result
    {
        $s3Client = self::setupS3Client();

        // Send a PutObject request and get the result object.
        return $s3Client->putObject([
            'Bucket' => AWS_BUCKET_NAME,
            'Key' => $fileName,
            'Body' => $file,
            'SourceFile' => $file,
            'ContentType' => 'audio/mpeg'
        ]);
    }

    /**
     * @param $fileName
     * @return \Aws\Result|string
     */
    public static function getFromS3($fileName)
    {
        $s3Client = self::setupS3Client();

        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => AWS_BUCKET_NAME,
            'Key' => $fileName
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+120 minutes');
        return (string)$request->getUri();
    }
}
