<?php

return [
    'name' => 'LaravelCore', 
    'app_frontend_link' => env('APP_FRONTEND_LINK', "localhost:4200"),
    'emailing' => [
        'welcome' => true,
        'forgot_password' => true,
        'get_started' => false,
    ],
    'email_templates' => [
        'forgot_password' => 'user::email.forgot-password-mail',
        'welcome' => 'user::email.welcome-mail',
    ], 
    'email_sender' => [
        'mail_from_address' => env("MAIL_FROM_ADDRESS", 'no-reply@techlify.com'),
        'mail_from_name' => env("MAIL_FROM_NAME", 'no-reply@techlify.com'),
    ]
];

// @todo Setup configuration to enable/disable social signup