<?php

return [
    'recipient' => env('PORTFOLIO_NOTIFICATION_EMAIL'),
    'errors_enabled' => env('PORTFOLIO_ERROR_EMAILS', false),
    'error_throttle_seconds' => 300,
    'contact_queue_connection' => env('PORTFOLIO_MAIL_QUEUE_CONNECTION', 'database'),
];
