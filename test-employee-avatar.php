<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\Shared\Document\Document;
use App\Services\GoogleCloudStorageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

$employeeId = 'd885a096-4a64-11ea-88e5-42010a940005';

echo "Testing Employee Avatar for ID: {$employeeId}\n";
echo str_repeat('=', 70)."\n\n";

// Clear cache
Cache::forget("document_url:{$employeeId}");

// 1. Check database
echo "1. Checking database...\n";
$document = Document::where('for_module_id', $employeeId)
    ->orderBy('created_at', 'desc')
    ->first();

if ($document) {
    echo "   ✓ Found in database\n";
    echo "   - ID: {$document->id_document}\n";
    echo "   - Module: {$document->for_module}\n";
    echo "   - File Name: {$document->file_name}\n";
    echo "   - Created: {$document->created_at}\n";
    $gcsPath = $document->getGcsPath();
    echo "   - GCS Path: {$gcsPath}\n";
} else {
    echo "   ✗ Not found in database\n";
    exit(1);
}

echo "\n";

// 2. Check GCS config
echo "2. Checking GCS Configuration...\n";
echo '   - Bucket: '.config('filesystems.disks.gcs.bucket')."\n";
echo '   - Key File: '.config('filesystems.disks.gcs.key_file')."\n";
echo '   - Project ID: '.config('filesystems.disks.gcs.project_id')."\n";

echo "\n";

// 3. Test with Storage facade
echo "3. Testing Storage facade directly...\n";
try {
    $exists = Storage::disk('gcs')->exists($gcsPath);
    echo "   - Storage::disk('gcs')->exists(): ".($exists ? 'YES' : 'NO')."\n";

    if (! $exists) {
        echo "   - Listing files in pegawai/ directory...\n";
        $files = Storage::disk('gcs')->files('pegawai');
        echo '   - Total files in pegawai/: '.count($files)."\n";

        // Check if any file contains the employee ID
        $employeeFiles = array_filter($files, function ($f) use ($employeeId) {
            return strpos($f, $employeeId) !== false;
        });

        if (! empty($employeeFiles)) {
            echo "   - Found files for this employee:\n";
            foreach (array_slice($employeeFiles, 0, 5) as $file) {
                echo "     • {$file}\n";
            }
        } else {
            echo "   - No files found for this employee ID in GCS\n";
        }
    }
} catch (\Exception $e) {
    echo '   ✗ Error: '.$e->getMessage()."\n";
    echo '   - Trace: '.$e->getFile().':'.$e->getLine()."\n";
}

echo "\n";

// 4. Test via GCS Service
echo "4. Testing via GoogleCloudStorageService...\n";
$gcsService = app(GoogleCloudStorageService::class);

try {
    $serviceExists = $gcsService->exists($gcsPath);
    echo '   - GcsService->exists(): '.($serviceExists ? 'YES' : 'NO')."\n";

    if ($serviceExists) {
        $url = $gcsService->getFileUrl($gcsPath);
        echo "   ✓ URL generated\n";
        echo "   - URL: {$url}\n";
    }
} catch (\Exception $e) {
    echo '   ✗ Error: '.$e->getMessage()."\n";
}

echo "\n";

// 5. Test via DocumentService
echo "5. Testing via DocumentService...\n";
$documentService = app(\App\Domains\Shared\Document\DocumentService::class);
$avatarUrl = $documentService->getEmployeeAvatarUrl($employeeId, 0);

echo "   - Avatar URL: {$avatarUrl}\n";
if (strpos($avatarUrl, 'etos.co.id') !== false) {
    echo "   - Type: Fallback image\n";
} else {
    echo "   - Type: GCS image\n";
}

echo "\n".str_repeat('=', 70)."\n";
