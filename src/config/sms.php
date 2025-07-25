<?php
return [
    'driver' => env('SMS_DRIVER', 'promotech'),
    'providers' => [
        'fake' => [
            'class' => \Mesalution\LaravelMesms\Providers\FakeSms::class,
            'options' => [
                'validOtp' => '123456',
            ]
        ],
        'promotech' => [
            'class' => \Mesalution\LaravelMesms\Providers\Promotech::class,
            'options' => [
                'url' => env('PROMOTECH_URL', 'http://apisms.promotech.co.th'),
                'username' => env('PROMOTECH_USERNAME'),
                'password' => env('PROMOTECH_PASSWORD'),
                'basicToken' => env('PROMOTECH_BASIC_TOKEN'),
                'otcId' => env('PROMOTECH_OTC_ID'),
                'senderName' => env('PROMOTECH_SENDER_NAME'),
            ],
        ]
    ]
];
