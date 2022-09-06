<?php

return [
    'decryption_key' => env('IAMSHARE_DK'),
    'model' => App\Models\User::class,
    'get_user_url' => env('IAMSHARE_GET_USER_URL'),
    'national_key' => env('IAMSHARE_NI', 'national_id'),
    'user_merge' => [
        'name' => 'arabic_name', // Merge to Created User from the Response
    ],
    'default_merge' => [
        'password' => '--' // Merge default Values.
    ]
];
