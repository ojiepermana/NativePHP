<?php

namespace App\Providers;

use App\Services\BniApiService;
use App\Services\BniEncryptionService;
use App\Services\GoogleCloudStorageManager;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GoogleCloudStorageManager::class, function ($app) {
            return new GoogleCloudStorageManager;
        });

        $this->app->singleton('bni-encryption', function () {
            return new BniEncryptionService(
                clientId: config('services.bni.client_id'),
                secret: config('services.bni.secret')
            );
        });

        $this->app->singleton(BniApiService::class, function ($app) {
            return new BniApiService(
                encryption: $app->make('bni-encryption'),
                baseUrl: config('services.bni.base_url'),
                prefix: config('services.bni.prefix'),
                timeout: config('services.bni.timeout', 30)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('gcs', function (Application $app, array $config) {
            $storageClient = new StorageClient([
                'projectId' => $config['project_id'],
                'keyFilePath' => $config['key_file'],
            ]);

            $bucket = $storageClient->bucket($config['bucket']);

            $adapter = new GoogleCloudStorageAdapter($bucket, $config['path_prefix'] ?? '');

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
