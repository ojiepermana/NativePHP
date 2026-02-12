<?php

// Example: Cara Menggunakan GCS Service dengan Laravel Style

use App\Facades\GCS;

// ============================================
// 1. Upload File
// ============================================

// Default disk (gcs)
GCS::uploadFile('documents/report.pdf', file_get_contents('/path/to/report.pdf'));

// Specific disk (gcs-generate)
GCS::disk('gcs-generate')->uploadFile('invoices/2026/invoice-001.html', '<html>Invoice content</html>');

// ============================================
// 2. Check File Existence
// ============================================

if (GCS::exists('documents/report.pdf')) {
    echo 'File exists!';
}

if (GCS::disk('gcs-generate')->exists('invoices/2026/invoice-001.html')) {
    echo 'Invoice exists!';
}

// ============================================
// 3. Get Public URL
// ============================================

$url = GCS::getFileUrl('documents/report.pdf');
// Returns: https://storage.googleapis.com/your-bucket/documents/report.pdf

$invoiceUrl = GCS::disk('gcs-generate')->getFileUrl('invoices/2026/invoice-001.html');
// Returns: https://storage.googleapis.com/generate-bucket/invoices/2026/invoice-001.html

// ============================================
// 4. Download File
// ============================================

GCS::downloadFile('documents/report.pdf', '/local/path/report.pdf');
GCS::disk('gcs-generate')->downloadFile('invoices/2026/invoice-001.html', '/tmp/invoice.html');

// ============================================
// 5. Delete File
// ============================================

GCS::deleteFile('documents/old-report.pdf');
GCS::disk('gcs-generate')->deleteFile('invoices/2025/old-invoice.html');

// ============================================
// 6. Get File Info
// ============================================

$size = GCS::getFileSize('documents/report.pdf'); // in bytes
$mimeType = GCS::getMimeType('documents/report.pdf'); // 'application/pdf'

// ============================================
// 7. List Files & Directories
// ============================================

$files = GCS::listFiles('documents/2026');
// ['documents/2026/report1.pdf', 'documents/2026/report2.pdf']

$directories = GCS::listDirectories('documents');
// ['documents/2025', 'documents/2026']

// ============================================
// 8. Copy & Move Files
// ============================================

GCS::copyFile('documents/report.pdf', 'archive/report-backup.pdf');
GCS::moveFile('documents/temp.pdf', 'documents/final.pdf');

// ============================================
// 9. Dalam Controller dengan DI
// ============================================

namespace App\Http\Controllers;

use App\Facades\GCS;
use App\Services\GoogleCloudStorageManager;

class DocumentController extends Controller
{
    public function __construct(
        private readonly GoogleCloudStorageManager $gcs
    ) {}

    public function upload(Request $request)
    {
        $file = $request->file('document');
        $path = 'documents/'.$file->hashName();

        // Via facade
        GCS::disk('gcs')->uploadFile($path, $file->getContent());

        // Via DI
        $this->gcs->disk('gcs')->uploadFile($path, $file->getContent());

        // Get URL
        $url = GCS::getFileUrl($path);

        return response()->json(['url' => $url]);
    }

    public function generateInvoice(string $id)
    {
        $html = view('invoice.template', ['id' => $id])->render();
        $path = "invoices/{$id}/invoice.html";

        // Upload to gcs-generate disk
        GCS::disk('gcs-generate')->uploadFile($path, $html);

        return GCS::disk('gcs-generate')->getFileUrl($path);
    }
}

// ============================================
// 10. Dalam Livewire Component
// ============================================

namespace App\Livewire;

use App\Facades\GCS;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUploader extends Component
{
    use WithFileUploads;

    public $photo;

    public function save()
    {
        $this->validate(['photo' => 'image|max:1024']);

        $path = 'photos/'.$this->photo->hashName();

        GCS::disk('gcs')->uploadFile($path, $this->photo->getContent());

        $url = GCS::getFileUrl($path);

        session()->flash('message', 'Photo uploaded: '.$url);
    }
}

// ============================================
// 11. Dalam Artisan Command
// ============================================

namespace App\Console\Commands;

use App\Facades\GCS;
use Illuminate\Console\Command;

class CleanupOldFiles extends Command
{
    protected $signature = 'gcs:cleanup';

    public function handle()
    {
        $files = GCS::disk('gcs')->listFiles('temp');

        foreach ($files as $file) {
            if ($this->isOld($file)) {
                GCS::disk('gcs')->deleteFile($file);
                $this->info("Deleted: {$file}");
            }
        }
    }
}
