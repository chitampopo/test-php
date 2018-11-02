<?php

use \yii\web\Request;

$baseUrl = str_replace('/application/web', '', (new Request)->getBaseUrl());
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-application',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'application\controllers',
    'components' => [
        'request' => [
            'baseUrl' => $baseUrl,
            'csrfParam' => '_csrf-application',
            'cookieValidationKey' => 'n9t-entmGGb6fPOnF2jUkrmsG1R8aR_0',
        ],
        'user' => [
            'class' => 'application\WebUser',
            'identityClass' => 'application\models\User\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-application', 'httpOnly' => true],
            'loginUrl' => ['/login']
        ],
        'session' => [
            // this is the name of the session cookie used for login on the application
            'name' => 'advanced-application',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace', 'info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'baseUrl' => $baseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
    'as access' => [
        'class' => 'application\AccessControl'
    ],
];
