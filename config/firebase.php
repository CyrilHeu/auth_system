<?php

return [
    'projects' => [
        'TABLETTE_GOURMANDE_DEV' => [
            'project_id' => env('FIREBASE_DEV_PROJECT_ID'),
            'web_api_key' => env('FIREBASE_DEV_WEB_API_KEY'),
        ],
        'TABLETTE_GOURMANDE_PROD' => [
            'project_id' => env('FIREBASE_PROD_PROJECT_ID'),
            'web_api_key' => env('FIREBASE_PROD_WEB_API_KEY'),
        ],
    ],
];
