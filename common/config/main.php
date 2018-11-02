<?php
return [
    'language' => 'vi',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['devicedetect'],
    'components' => [
        'db' => require __DIR__ . '/dbconfig.php',
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
                ],
            ]
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.aiaexchangecantho.com',
                'username' => 'system@aiaexchangecantho.com',
                'password' => 'Z+@X]yE=k+jS',
                'port' => '587',
                'encryption' => 'tls'
            ],
        ]
    ]
];
