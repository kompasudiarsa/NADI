<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'transmedika' => [
    'token' => env('TRANSMEDIKA_TOKEN'),
],
'queue_api' => [
    'base_url' => env(
        'API_BASE_URL',
        'http://127.0.0.1:8000'
    ),

    'token' => env(
        'API_TOKEN',
        'rsbm-client-01'
    ),

    'secret' => env(
        'API_SECRET'
    ),
],
'ereservasi' => [
    'base_url' => env(
        'ERESERVASI_BASE_URL',
        'https://app.balimandarahospital.com'
    ),
'endpoint' => env(
        'ERESERVASI_ENDPOINT',
        '/service/medifirst2000/reservasionline/get-history-ereservasi'
    ),
    'token' => env('TRANSMEDIKA_TOKEN'),

    'timeout' => (int) env(
        'ERESERVASI_TIMEOUT',
        15
    ),
],
'bpjs' => [
    'vclaim_base_url' => env('BPJS_VCLAIM_BASE_URL'),
    'antrean_base_url' => env('BPJS_ANTREAN_BASE_URL'),

    'cons_id' => env('BPJS_CONS_ID'),
    'secret_key' => env('BPJS_SECRET_KEY'),
    'user_key' => env('BPJS_USER_KEY'),

    'timeout' => env('BPJS_TIMEOUT_SECONDS', 15),
],
];
