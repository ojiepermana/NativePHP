<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\GoogleCloudStorageManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\GoogleCloudStorageService disk(string $name = 'gcs')
 * @method static bool exists(string $path)
 * @method static string getFileUrl(string $path)
 * @method static bool uploadFile(string $path, mixed $contents)
 * @method static bool downloadFile(string $gcsPath, string $localPath)
 * @method static bool deleteFile(string $path)
 * @method static int getFileSize(string $path)
 * @method static string|false getMimeType(string $path)
 * @method static array listFiles(string $directory = '')
 * @method static array listDirectories(string $directory = '')
 * @method static bool copyFile(string $from, string $to)
 * @method static bool moveFile(string $from, string $to)
 *
 * @see \App\Services\GoogleCloudStorageManager
 */
class GCS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GoogleCloudStorageManager::class;
    }
}
