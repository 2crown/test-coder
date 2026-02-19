<?php

return [
    'api' => [
        'driver' => 'throttle',
        'provider' => 'users',
        'limit' => 60,
        'decay_minutes' => 1,
    ],

    'login' => [
        'driver' => 'throttle',
        'provider' => 'users',
        'limit' => 5,
        'decay_minutes' => 1,
    ],

    'registration' => [
        'driver' => 'throttle',
        'provider' => 'users',
        'limit' => 3,
        'decay_minutes' => 60,
    ],
];
