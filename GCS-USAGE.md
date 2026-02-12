# Google Cloud Storage Service - Laravel Style

## Setup

Service ini sudah otomatis teregister di `AppServiceProvider`.

## Penggunaan

### 1. Menggunakan Facade (Recommended)

```php
use App\Facades\GCS;

// Menggunakan default disk (gcs)
GCS::uploadFile('path/to/file.txt', 'contents');
GCS::exists('path/to/file.txt');
$url = GCS::getFileUrl('path/to/file.txt');

// Menggunakan disk spesifik (gcs-generate)
GCS::disk('gcs-generate')->uploadFile('invoice/test.html', '<html>...</html>');
GCS::disk('gcs-generate')->exists('invoice/test.html');
$url = GCS::disk('gcs-generate')->getFileUrl('invoice/test.html');

// Method lainnya
GCS::deleteFile('path/to/file.txt');
GCS::downloadFile('remote/file.txt', '/local/path/file.txt');
$size = GCS::getFileSize('path/to/file.txt');
$mimeType = GCS::getMimeType('path/to/file.txt');
$files = GCS::listFiles('directory/path');
$directories = GCS::listDirectories('directory/path');
GCS::copyFile('from/path.txt', 'to/path.txt');
GCS::moveFile('from/path.txt', 'to/path.txt');
```

### 2. Menggunakan Dependency Injection

```php
use App\Services\GoogleCloudStorageManager;

class YourController
{
    public function __construct(
        private readonly GoogleCloudStorageManager $gcs
    ) {}
    
    public function upload()
    {
        $this->gcs->disk('gcs')->uploadFile('path/file.txt', 'content');
        
        // atau default disk
        $this->gcs->uploadFile('path/file.txt', 'content');
    }
}
```

### 3. Menggunakan Service Container

```php
$gcs = app(GoogleCloudStorageManager::class);
$gcs->disk('gcs-generate')->uploadFile('path/file.txt', 'content');
```

## Available Methods

- `exists(string $path): bool` - Check if file exists
- `getFileUrl(string $path): string` - Get public URL
- `uploadFile(string $path, mixed $contents): bool` - Upload file
- `downloadFile(string $gcsPath, string $localPath): bool` - Download file
- `deleteFile(string $path): bool` - Delete file
- `getFileSize(string $path): int` - Get file size in bytes
- `getMimeType(string $path): string|false` - Get mime type
- `listFiles(string $directory = ''): array` - List files in directory
- `listDirectories(string $directory = ''): array` - List directories
- `copyFile(string $from, string $to): bool` - Copy file
- `moveFile(string $from, string $to): bool` - Move file

## Multiple Disks

Service ini mendukung multiple GCS disks yang dikonfigurasi di `config/filesystems.php`:

```php
// Menggunakan disk 'gcs'
GCS::disk('gcs')->uploadFile('file.txt', 'content');

// Menggunakan disk 'gcs-generate'
GCS::disk('gcs-generate')->uploadFile('file.txt', 'content');
```

## Benefits

- ✅ Laravel-style API yang familiar
- ✅ Support multiple disks
- ✅ Bypass Flysystem ACL issues
- ✅ Compatible dengan Uniform Bucket-Level Access
- ✅ Type-safe dengan PHP 8.1+
- ✅ Auto path prefix handling
