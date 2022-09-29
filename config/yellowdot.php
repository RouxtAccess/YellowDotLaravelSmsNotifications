<?php

/**
 * The YellowDot api credentials
 */

return [
    'api' => [
        'host' => env('YELLOW_DOT_HOST', 'https://api.ydplatform.com/api/'),
        'password' => env('YELLOW_DOT_PASSWORD', ),
    ],
    'sms' => [
        'delivery_enabled' => env('YELLOW_DOT_SMS_DELIVERY_ENABLED', false),
        'delivery_reports' => [
            'enabled' => env('YELLOW_DOT_SMS_DELIVERY_REPORTS_ENABLED', false),
        ],
    ],
];
