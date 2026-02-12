# Google Cloud Storage Setup

Google Cloud Storage (GCS) telah berhasil dikonfigurasi pada aplikasi Laravel ini.

## Konfigurasi

### Environment Variables

File `.env` sudah dikonfigurasi dengan variabel berikut:

```env
GCS_PROJECT_ID=etosindonusa
GCS_KEY_FILE=etos-generate-gcs.json
GCS_BUCKET=etos-upload
GCS_PATH_PREFIX=
```

### Credential File

Credential file disimpan di: `storage/etos-generate-gcs.json`

### Package yang Diinstall

- `league/flysystem-google-cloud-storage` (v3.31.0)
- `google/cloud-storage` (v1.49.2)

## Cara Penggunaan

### Upload File ke GCS

```php
use Illuminate\Support\Facades\Storage;

// Upload file
Storage::disk('gcs')->put('folder/file.txt', 'Contents');

// Upload file dari request
$path = $request->file('avatar')->store('avatars', 'gcs');

// Upload dengan nama spesifik
Storage::disk('gcs')->putFileAs('avatars', $request->file('avatar'), 'avatar.jpg');
```

### Membaca File dari GCS

```php
// Mendapatkan konten file
$contents = Storage::disk('gcs')->get('folder/file.txt');

// Cek apakah file exists
if (Storage::disk('gcs')->exists('file.txt')) {
    // File exists
}

// Mendapatkan daftar file
$files = Storage::disk('gcs')->files('folder');

// Mendapatkan daftar direktori
$directories = Storage::disk('gcs')->directories('folder');
```

### Menghapus File dari GCS

```php
// Hapus single file
Storage::disk('gcs')->delete('folder/file.txt');

// Hapus multiple files
Storage::disk('gcs')->delete(['file1.txt', 'file2.txt']);
```

### Mendapatkan URL File

```php
// Mendapatkan public URL
$url = Storage::disk('gcs')->url('folder/file.txt');

// Temporary signed URL (untuk private files)
$url = Storage::disk('gcs')->temporaryUrl('folder/file.txt', now()->addMinutes(30));
```

### Download File

```php
// Download file
return Storage::disk('gcs')->download('folder/file.txt');

// Download dengan nama custom
return Storage::disk('gcs')->download('folder/file.txt', 'custom-name.txt');
```

### Operasi File Lainnya

```php
// Copy file
Storage::disk('gcs')->copy('old.txt', 'new.txt');

// Move file
Storage::disk('gcs')->move('old.txt', 'new.txt');

// Mendapatkan size file
$size = Storage::disk('gcs')->size('file.txt');

// Mendapatkan last modified time
$time = Storage::disk('gcs')->lastModified('file.txt');
```

## Menggunakan GCS sebagai Default Disk

Jika Anda ingin menggunakan GCS sebagai default storage disk, ubah di file `.env`:

```env
FILESYSTEM_DISK=gcs
```

Kemudian Anda dapat menggunakan Storage tanpa menentukan disk:

```php
// Akan menggunakan GCS sebagai default
Storage::put('file.txt', 'Contents');
```

## Testing

Untuk memverifikasi koneksi GCS, gunakan Laravel Tinker:

```bash
php artisan tinker
```

```php
// Test koneksi
Storage::disk('gcs')->files();

// Test upload
Storage::disk('gcs')->put('test.txt', 'Hello GCS!');

// Test read
Storage::disk('gcs')->get('test.txt');
```

## Troubleshooting

### Error: "Unable to authenticate"

Pastikan file credential `storage/etos-generate-gcs.json` ada dan dapat diakses.

### Error: "Bucket not found"

Pastikan nama bucket di `.env` benar dan bucket tersebut ada di GCS project Anda.

### Permission Issues

Pastikan service account memiliki permission yang cukup:
- `storage.objects.create`
- `storage.objects.delete`
- `storage.objects.get`
- `storage.objects.list`

## Security Notes

⚠️ **Penting**: Jangan commit file credential (`storage/etos-generate-gcs.json`) ke repository. File ini sudah seharusnya ada di `.gitignore`.

Pastikan file `.gitignore` berisi:

```
storage/*.json
```
