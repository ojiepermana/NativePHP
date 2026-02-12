<?php

declare(strict_types=1);

namespace App\Services;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use RuntimeException;

class GoogleCloudStorageService
{
    private StorageClient $client;

    private Bucket $bucket;

    private string $bucketName;

    private string $pathPrefix;

    private string $diskName;

    public function __construct(string $diskName = 'gcs-upload')
    {
        $this->diskName = $diskName;
        $diskConfig = $this->getGcsConfiguration();

        $this->client = new StorageClient([
            'keyFilePath' => $diskConfig['key_file'],
            'projectId' => $diskConfig['project_id'],
        ]);

        $this->bucketName = $diskConfig['bucket'];
        $this->bucket = $this->client->bucket($this->bucketName);
        $this->pathPrefix = $diskConfig['path_prefix'] ?? '';
    }

    /**
     * Get and validate GCS configuration.
     */
    private function getGcsConfiguration(): array
    {
        $diskConfig = config("filesystems.disks.{$this->diskName}");

        if (! $diskConfig) {
            throw new RuntimeException("GCS disk [{$this->diskName}] is not configured in filesystems.php");
        }

        if (empty($diskConfig['bucket'])) {
            throw new RuntimeException("GCS bucket for disk [{$this->diskName}] is not configured");
        }

        if (empty($diskConfig['key_file']) || ! file_exists($diskConfig['key_file'])) {
            throw new RuntimeException("GCS key file not found for disk [{$this->diskName}]: ".($diskConfig['key_file'] ?? 'not set'));
        }

        return $diskConfig;
    }

    /**
     * Build full path with prefix.
     */
    private function buildPath(string $path): string
    {
        $path = ltrim($path, '/');

        if ($this->pathPrefix) {
            return trim($this->pathPrefix, '/').'/'.$path;
        }

        return $path;
    }

    /**
     * Check if file exists in GCS.
     */
    public function exists(string $path): bool
    {
        $fullPath = $this->buildPath($path);

        return $this->bucket->object($fullPath)->exists();
    }

    /**
     * Get public URL for a file.
     */
    public function getFileUrl(string $path): string
    {
        $fullPath = $this->buildPath($path);

        return "https://storage.googleapis.com/{$this->bucketName}/{$fullPath}";
    }

    /**
     * Upload file to GCS.
     */
    public function uploadFile(string $path, mixed $contents): bool
    {
        try {
            $fullPath = $this->buildPath($path);

            $this->bucket->upload($contents, [
                'name' => $fullPath,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Download file from GCS to local path.
     */
    public function downloadFile(string $gcsPath, string $localPath): bool
    {
        try {
            $fullPath = $this->buildPath($gcsPath);
            $object = $this->bucket->object($fullPath);

            if (! $object->exists()) {
                return false;
            }

            $contents = $object->downloadAsString();

            return file_put_contents($localPath, $contents) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete file from GCS.
     */
    public function deleteFile(string $path): bool
    {
        try {
            $fullPath = $this->buildPath($path);
            $object = $this->bucket->object($fullPath);

            if (! $object->exists()) {
                return false;
            }

            $object->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file size in bytes.
     */
    public function getFileSize(string $path): int
    {
        $fullPath = $this->buildPath($path);
        $object = $this->bucket->object($fullPath);

        if (! $object->exists()) {
            return 0;
        }

        $info = $object->info();

        return (int) ($info['size'] ?? 0);
    }

    /**
     * Get file mime type.
     */
    public function getMimeType(string $path): string|false
    {
        try {
            $fullPath = $this->buildPath($path);
            $object = $this->bucket->object($fullPath);

            if (! $object->exists()) {
                return false;
            }

            $info = $object->info();

            return $info['contentType'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * List all files in a directory.
     */
    public function listFiles(string $directory = ''): array
    {
        $fullPath = $this->buildPath($directory);
        $prefix = $fullPath ? rtrim($fullPath, '/').'/' : '';

        $objects = $this->bucket->objects([
            'prefix' => $prefix,
            'delimiter' => '/',
        ]);

        $files = [];
        foreach ($objects as $object) {
            $files[] = $object->name();
        }

        return $files;
    }

    /**
     * List all directories in a directory.
     */
    public function listDirectories(string $directory = ''): array
    {
        $fullPath = $this->buildPath($directory);
        $prefix = $fullPath ? rtrim($fullPath, '/').'/' : '';

        $objects = $this->bucket->objects([
            'prefix' => $prefix,
            'delimiter' => '/',
        ]);

        $directories = [];
        foreach ($objects->prefixes() as $prefix) {
            $directories[] = rtrim($prefix, '/');
        }

        return $directories;
    }

    /**
     * Copy file within GCS.
     */
    public function copyFile(string $from, string $to): bool
    {
        try {
            $fromPath = $this->buildPath($from);
            $toPath = $this->buildPath($to);

            $sourceObject = $this->bucket->object($fromPath);

            if (! $sourceObject->exists()) {
                return false;
            }

            $sourceObject->copy($this->bucket, [
                'name' => $toPath,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move file within GCS.
     */
    public function moveFile(string $from, string $to): bool
    {
        try {
            $fromPath = $this->buildPath($from);
            $toPath = $this->buildPath($to);

            $sourceObject = $this->bucket->object($fromPath);

            if (! $sourceObject->exists()) {
                return false;
            }

            // Copy to new location
            $sourceObject->copy($this->bucket, [
                'name' => $toPath,
            ]);

            // Delete original
            $sourceObject->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
