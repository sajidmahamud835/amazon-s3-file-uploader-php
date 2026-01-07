<?php

namespace App;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use finfo;

class S3Uploader
{
    private S3Client $s3;
    private string $bucket;

    public function __construct(string $bucket, string $region, string $accessKey, string $secretKey)
    {
        $this->bucket = $bucket;
        $this->s3 = new S3Client([
            'version' => '2006-03-01',
            'region'  => $region,
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }

    public function upload(array $file, array $allowedExtensions = [], int $maxSize = 5242880): string
    {
        // 1. Check upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Upload failed with error code: " . $file['error']);
        }

        // 2. Size Validation
        if ($file['size'] > $maxSize) {
            throw new \RuntimeException("File size exceeds limit (" . ($maxSize / 1024 / 1024) . "MB).");
        }

        // 3. MIME Validation
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Default allowed map
        if (empty($allowedExtensions)) {
            $allowedExtensions = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'application/pdf' => 'pdf',
                'text/plain' => 'txt'
            ];
        }

        if (!isset($allowedExtensions[$mimeType])) {
            throw new \RuntimeException("Invalid file type ($mimeType). Allowed: " . implode(', ', array_unique($allowedExtensions)));
        }

        $extension = $allowedExtensions[$mimeType];
        $uniqueName = uniqid('upload_', true) . '.' . $extension;

        // 4. Upload
        try {
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $uniqueName,
                'SourceFile' => $file['tmp_name'],
                'ACL'    => 'private',
            ]);

            return $result['ObjectURL'];

        } catch (AwsException $e) {
            // Rethrow AwsException for proper handling upstream
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException("Upload Error: " . $e->getMessage());
        }
    }

    /**
     * Test the S3 connection by attempting to list bucket contents.
     * @throws AwsException if credentials are invalid or bucket doesn't exist
     */
    public function testConnection(): bool
    {
        // Try to head the bucket - this validates credentials and bucket existence
        $this->s3->headBucket([
            'Bucket' => $this->bucket
        ]);
        return true;
    }
}
