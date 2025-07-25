<?php
return [
    'driver' => env('SMS_DRIVER', 'promotech'),
    'providers' => [
        'fake' => [
            'class' => \Mesalution\Sms\Providers\FakeSms::class,
            'options' => []
        ],
        'promotech' => [
            'class' => \Mesalution\Sms\Providers\Promotech::class,
            'options' => [
                'url' => env('PROMOTECH_URL', 'http://apisms.promotech.co.th'),
                'username' => env('PROMOTECH_USERNAME'),
                'password' => env('PROMOTECH_PASSWORD'),
                'otcId' => env('PROMOTECH_OTC_ID'),
                'senderName' => env('PROMOTECH_SENDER_NAME'),
            ],
        ]
    ]
];
