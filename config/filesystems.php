<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        'gcs-generate' => [
            'driver' => 'gcs',
            'project_id' => env('GCS_GENERATE_PROJECT_ID', 'your-project-id'),
            'key_file' => env('GCS_GENERATE_KEY_FILE') ? storage_path(env('GCS_GENERATE_KEY_FILE')) : null,
            'bucket' => env('GCS_GENERATE_BUCKET', 'your-bucket'),
            'path_prefix' => env('GCS_GENERATE_PATH_PREFIX', ''),
            'storage_api_uri' => env('GCS_STORAGE_API_URI', null),
            'apiEndpoint' => env('GCS_API_ENDPOINT', null),
            'uniform_bucket_level_access' => true,
            'metadata' => [
                'predefinedAcl' => '',
            ],
            'url' => env('GCS_GENERATE_URL', 'https://storage.googleapis.com/'.env('GCS_GENERATE_BUCKET')),
            'throw' => true,
            'report' => true,
        ],

        'gcs-upload' => [
            'driver' => 'gcs',
            'project_id' => env('GCS_UPLOAD_PROJECT_ID', 'your-project-id'),
            'key_file' => env('GCS_UPLOAD_KEY_FILE') ? storage_path(env('GCS_UPLOAD_KEY_FILE')) : null,
            'bucket' => env('GCS_UPLOAD_BUCKET', 'your-bucket'),
            'path_prefix' => env('GCS_UPLOAD_PATH_PREFIX', ''),
            'storage_api_uri' => env('GCS_STORAGE_API_URI', null),
            'apiEndpoint' => env('GCS_API_ENDPOINT', null),
            'metadata' => [],
            'url' => env('GCS_UPLOAD_URL', 'https://storage.googleapis.com/'.env('GCS_UPLOAD_BUCKET')),
            'throw' => true,
            'report' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
