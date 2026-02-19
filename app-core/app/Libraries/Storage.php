<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class Storage
{
    protected $driver;
    protected $uploadPath;

    public function __construct()
    {
        $this->driver = env('STORAGE_DRIVER', 'local');
        
        // Determine physical upload path
        // Priority: Env STORAGE_PATH -> FCPATH
        $this->uploadPath = rtrim(env('STORAGE_PATH', FCPATH), '/\\') . '/';

        if ($this->driver === 's3') {
            if (!class_exists('\Aws\S3\S3Client')) {
                log_message('error', 'AWS SDK not found. Please run composer require aws/aws-sdk-php. Falling back to local storage.');
                $this->driver = 'local';
            } else {
                $this->s3Client = new S3Client([
                    'version' => 'latest',
                    'region'  => env('S3_REGION'),
                    'credentials' => [
                        'key'    => env('S3_KEY'),
                        'secret' => env('S3_SECRET'),
                    ],
                    'endpoint' => env('S3_ENDPOINT'), // For Minio or other S3 compatibles
                    'use_path_style_endpoint' => true,
                ]);
                $this->bucket = env('S3_BUCKET');
            }
        }
    }

    /**
     * Upload a file
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $path Target directory/prefix
     * @return string|null Filename/Key on success, null on failure
     */
    public function upload($file, $path = 'uploads')
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();

        if ($this->driver === 's3') {
            try {
                $result = $this->s3Client->putObject([
                    'Bucket' => $this->bucket,
                    'Key'    => $path . '/' . $newName,
                    'Body'   => fopen($file->getTempName(), 'r'),
                    'ACL'    => 'public-read',
                ]);
                return $path . '/' . $newName;
            } catch (AwsException $e) {
                log_message('error', 'S3 Upload Error: ' . $e->getMessage());
                return null;
            }
        } else {
            // Local
            // Use configured uploadPath instead of FCPATH directly
            if ($file->move($this->uploadPath . $path, $newName)) {
                return $path . '/' . $newName;
            }
        }

        return null;
    }

    /**
     * Delete a file
     * 
     * @param string $path Full path/key of the file
     * @return bool
     */
    public function delete($path)
    {
        if (empty($path)) return false;

        if ($this->driver === 's3') {
            try {
                $this->s3Client->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key'    => $path,
                ]);
                return true;
            } catch (AwsException $e) {
                log_message('error', 'S3 Delete Error: ' . $e->getMessage());
                return false;
            }
        } else {
            $fullPath = $this->uploadPath . $path;
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
        }

        return false;
    }

    /**
     * Get public URL of a file
     * 
     * @param string $path Full path/key of the file
     * @return string
     */
    public function url($path)
    {
        if (empty($path)) return '';

        if ($this->driver === 's3') {
            return $this->s3Client->getObjectUrl($this->bucket, $path);
        }

        return asset_url($path);
    }
}
