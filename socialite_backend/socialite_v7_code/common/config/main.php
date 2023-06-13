<?php
$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);




return [
    'name'=>'SocialiFied',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManagerFrontend' => [
            'class' => 'yii\web\UrlManager',
        	'baseUrl' => '/',
        	'enablePrettyUrl' => true,
        	'showScriptName' => false,
    	],
       'formatter' => [
                'dateFormat' => 'dd/MM/yyyy',
                'decimalSeparator' => ',',
                'thousandSeparator' => ' ',
                'currencyCode' => 'EUR',
                'datetimeFormat' => 'dd/MM/yyyy h:mm a',
                'timeFormat' => 'h:i:s A',
        ],
        's3' => [
            'class' => 'frostealth\yii2\aws\s3\Service',
            'credentials' => [ // Aws\Credentials\CredentialsInterface|array|callable
                'key' => $params['s3']['key'],
                'secret' => $params['s3']['secret'],
            ],
            'region' => $params['s3']['region'],
            'defaultBucket' => $params['s3']['defaultBucket'],
            'defaultAcl' => 'public-read',
        ],
       
        'pushNotification' => [
            'class' => 'common\components\PushNotification'
        ],
        
       
    
                
    ],
    'modules' => [
        'gridview' => ['class' => 'kartik\grid\Module'],
       
   ],
   'params' => $params,
];
