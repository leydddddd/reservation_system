<?php
// filepath: c:\Users\barak\Documents\AKUKODING\PROKON\bckup\web\api\config.php
return [
    'midtrans' => [
        'server_key' => getenv('MIDTRANS_SERVER_KEY') ?: '',
        'client_key' => getenv('MIDTRANS_CLIENT_KEY') ?: '',
        'is_production' => false,
    ]
];
