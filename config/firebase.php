<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Specify the path to your Firebase service account credentials JSON file.
    | This file is required to authenticate your Laravel app with Firebase.
    |
    */
    
    'credentials' => env('FIREBASE_CREDENTIALS', base_path('firebase_credentials.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Realtime Database URL
    |--------------------------------------------------------------------------
    |
    | Define the URL of your Firebase Realtime Database.
    | This is required if you want to interact with the database.
    |
    */
    
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://your-project.firebaseio.com'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM)
    |--------------------------------------------------------------------------
    |
    | Define settings for Firebase Cloud Messaging (FCM).
    |
    */
    
    'messaging' => [
        'default' => [
            'server_key' => env('FIREBASE_SERVER_KEY', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Dynamic Links
    |--------------------------------------------------------------------------
    |
    | Define settings for Firebase Dynamic Links if used.
    |
    */
    
    'dynamic_links' => [
        'domain' => env('FIREBASE_DYNAMIC_LINKS_DOMAIN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage
    |--------------------------------------------------------------------------
    |
    | Specify the default bucket for Firebase Cloud Storage.
    |
    */
    
    'storage' => [
        'default_bucket' => env('FIREBASE_STORAGE_BUCKET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Define if Firebase data should be cached.
    |
    */
    
    'cache' => [
        'store' => env('FIREBASE_CACHE_STORE', 'file'),
        'ttl'   => env('FIREBASE_CACHE_TTL', 60),
    ],

];
