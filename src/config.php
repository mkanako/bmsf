<?php

return [
    'auth' => [
        'provider' => [
            'driver' => 'eloquent',
            'model' => Cc\Bmsf\Models\User::class,
        ],
    ],
    'attachment' => [
        'allowed_ext' => [
            'image' => 'gif|png|jpe?g',
            'video' => 'og?|mp4|webm|mp?g|mov|3gp',
            'audio' => 'og?|mp3|mp?g|wav',
        ],
        'disk' => [
            'driver' => 'local',
            'root' => storage_path('app/attachments/{{prefix}}'),
            'url' => env('APP_URL') . '/attachments/{{prefix}}',
            'visibility' => 'public',
        ],
    ],
];
