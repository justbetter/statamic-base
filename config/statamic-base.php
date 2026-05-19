<?php

return [

    'permissions' => [
        'view' => 'view justbetter packages',
    ],

    'packagist_cache_ttl' => (int) env('STATAMIC_BASE_PACKAGIST_CACHE_TTL', 3600),

    'icon_url' => 'https://opensource.justbetter.nl/statamic/justbetter-logo-small-black.svg',

    'icon_dark_url' => 'https://opensource.justbetter.nl/statamic/justbetter-logo-small-white.svg',
];
