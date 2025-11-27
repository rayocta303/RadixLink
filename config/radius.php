<?php

return [
    'server' => env('RADIUS_SERVER', '127.0.0.1'),
    'secret' => env('RADIUS_SECRET', 'radiussecret'),
    'auth_port' => env('RADIUS_AUTH_PORT', 1812),
    'acct_port' => env('RADIUS_ACCT_PORT', 1813),
    'coa_port' => env('RADIUS_COA_PORT', 3799),
    'timeout' => env('RADIUS_TIMEOUT', 3000),
    
    'attributes' => [
        'password' => 'Cleartext-Password',
        'expiration' => 'Expiration',
        'simultaneous_use' => 'Simultaneous-Use',
        'session_timeout' => 'Session-Timeout',
        'idle_timeout' => 'Idle-Timeout',
        'mikrotik_rate_limit' => 'Mikrotik-Rate-Limit',
        'framed_ip' => 'Framed-IP-Address',
        'framed_pool' => 'Framed-Pool',
    ],
];
