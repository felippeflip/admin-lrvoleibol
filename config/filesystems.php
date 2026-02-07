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
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
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
        ],

        'times_logos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/times_logos'), 
            'url' => '/storage/times_logos',
            'visibility' => 'public',
        ],

        'atletas_fotos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/atletas_fotos'), 
            'url' => '/storage/atletas_fotos', 
            'visibility' => 'public',
        ],

        'comissao_fotos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/comissao_fotos'),
            'url' => '/storage/comissao_fotos',
            'visibility' => 'public',
        ],

        'comissao_docs' => [
            'driver' => 'local',
            'root' => storage_path('app/public/comissao_docs'),
            'url' => '/storage/comissao_docs',
            'visibility' => 'public',
        ],

        'user_fotos' => [
            'driver' => 'local',
            'root' => storage_path('app/public/user_fotos'),
            'url' => '/storage/user_fotos',
            'visibility' => 'public',
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
