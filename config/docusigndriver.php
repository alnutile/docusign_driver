<?php

return [
    'api_token' => env('DOCUSIGN_API_TOKEN'),
    'url' => env('DOCUSIGN_URL', 'https://docuseal.co'),
    'rest_url' => env('DOCUSIGN_URL', 'https://demo.docusign.net'),
    'host' => 'https://demo.docusign.net/restapi',
    'base_url' => env('DOCUSIGN_BASE_URL', 'https://account-d.docusign.com'),
    //https://account.docusign.com
    //https://account-d.docusign.com
    //https://account-s.docusign.com
    'auth_host' => env('DOCUSIGN_AUTH_HOST', 'https://account-d.docusign.com/oauth/token'),
    'username' => env('DOCUSIGN_USERNAME'),
    'user_id' => env('DOCUSIGN_USER_ID'),
    'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
    'expires_in' => env('DOCUSIGN_EXPIRES_IN', 60),
    'private_key' => env('DOCUSIGN_PRIVATE_KEY'),
    'rsa_private_key' => env('DOCUSIGN_RSA_PRIVATE_KEY'),
    'rsa_public_key' => env('DOCUSIGN_RSA_PUBLIC_KEY'),
    'password' => env('DOCUSIGN_PASSWORD'),
    'integrator_key' => env('DOCUSIGN_INTEGRATOR_KEY'),
];
