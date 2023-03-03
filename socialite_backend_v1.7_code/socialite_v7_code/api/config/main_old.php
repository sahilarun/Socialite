<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),    
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'components' => [       
        'pushNotification' => [
            'class' => 'api\components\PushNotification'
        ],
        
        'user' => [
            'identityClass' => 'api\modules\v1\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
                
            ],
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'xxxxxxx',
        ],
        'response'  =>  [
            
            'format'        =>  'json',
            'class'         =>  'yii\web\Response',
            'on beforeSend' =>  function ($event) {
            $response = $event->sender;


                if ($response->data !== null && $response->statusCode != 401 && $response->statusCode != 404 && $response->statusCode != 405 && $response->statusCode != 500  ) {
                //if ($response->data !== null && $response->statusCode != 401 && $response->statusCode != 404 && $response->statusCode != 405   ) {
                    $message= isset($response->data['message'])? $response->data['message']:'';
                    $response->statusCode=  $statusCode=isset($response->data['statusCode'])?$response->data['statusCode']:$response->statusCode;
                    if(isset($response->data['message']))
                    unset($response->data['message']);
                    if(isset($response->data['statusCode']))
                    unset($response->data['statusCode']);

                    $response->data = [
                        //'isSuccessful'      =>  $response->isSuccessful,
                        //'isOk'              =>  $response->isOk,
                        //'isServerError'     =>  $response->isServerError,
                        'status'            =>  $statusCode,
                        'statusText'        =>  $response->statusText,
                        'message'           =>  $message,
                        'data'              =>  $response->data,                                    
                    ];                
                }
            },

        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/photo',
                    'extraPatterns' => [
                       
                        'POST login' 		    => 'login',
                       
                    
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/country',
                    'extraPatterns' => [
                        //'GET test' 		    => 'test',
                        //'POST login' 		    => 'login',
                       
                    
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'extraPatterns' => [
                        'GET test' 		            => 'test',
                        'POST login' 		        => 'login',
                        'POST logout' 		        => 'logout',
                        'POST login-social' 		=> 'login-social',
                        'POST forgot-password' 	    => 'forgot-password',
                        'POST register' 		    => 'register',
                        'GET profile'    		    => 'profile',
                        'POST profile-update'       => 'profile-update',
                        'POST update-token'       => 'update-token',
                        'POST update-location'       => 'update-location',
                        'POST update-password'       => 'update-password',
                        'POST update-payment-detail' =>'update-payment-detail',
                        'POST update-profile-image'       => 'update-profile-image',
                        'GET nearest-user'          => 'nearest-user',
                        'POST update-mobile'          => 'update-mobile',
                        'POST verify-otp'          => 'verify-otp',
                        'POST change-mobile'          => 'change-mobile',
                        'POST search-user'          => 'search-user',
                        'GET find-friend'          => 'find-friend',
                        'POST report-user'  => 'report-user', 
                        'POST verify-registration-otp' => 'verify-registration-otp',
                        'POST check-username' => 'check-username',

                        'POST forgot-password-request' 	=> 'forgot-password-request',
                        'POST forgot-password-verify-otp' 	=> 'forgot-password-verify-otp',
                        'POST set-new-password' 	=> 'set-new-password',
                        'POST resend-otp'           => 'resend-otp',
                        'GET sugested-user'          => 'sugested-user',
                        'POST push-notification-status'           => 'push-notification-status',
                        'POST delete-account'           => 'delete-account'
                        
                        
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/category',
                    'extraPatterns' => [
                        'GET live-tv'               => 'live-tv',
                        'GET gift'                  => 'gift'
                       
                    
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/state',
                    'extraPatterns' => [
                       
                    
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/city',
                    'extraPatterns' => [
                       
                    
                    ],
                ],
                
                
                /*
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/ad',
                    'extraPatterns' => [
                        'POST upload-image'       => 'upload-image',
                        'GET my-ad'               => 'my-ad',
                        'POST update-status'       => 'update-status',
                        'POST ad-search'       => 'ad-search',
                        'POST report-ad'       => 'report-ad',
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/package',
                    'extraPatterns' => [],
                ],*/
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/favorite',
                    'extraPatterns' => [
                        'POST delete-list'       => 'delete-list',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/payment',
                    'extraPatterns' => [
                        'POST package-subscription'     => 'package-subscription',
                        'POST withdrawal'               => 'withdrawal',
                        'GET withdrawal-history'        => 'withdrawal-history',
                        'GET payment-history'           => 'payment-history',
                        'POST redeem-coin'               => 'redeem-coin'
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/follower',
                    'extraPatterns' => [
                        'POST unfollow'  => 'unfollow',
                        'POST follow-multiple'  => 'follow-multiple',
                        'GET my-follower'  => 'my-follower',
                        'GET my-following-live'  => 'my-following-live',
                        'GET my-following'  => 'my-following',
                        
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/message',
                    'extraPatterns' => [
                        'GET message-group'            => 'message-group',
                        'GET message-history'            => 'message-history',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/audio',
                    'extraPatterns' => [
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/post',
                    'extraPatterns' => [
                        'GET my-post'            => 'my-post',  
                        'GET search-post'        => 'search-post',  
                        'GET story-post'         => 'story-post',  
                        'GET my-story-post'      => 'my-story-post',  
                        'GET search-post-following-user'        => 'search-post-following-user',  
                        'GET my-post-mention-user'            => 'my-post-mention-user',  
                        'POST like'              => 'like',  
                        'POST unlike'            => 'unlike',  
                        'POST view-counter'      => 'view-counter', 
                        'POST add-comment'      => 'add-comment',
                        'GET comment-list'      => 'comment-list', 
                        'POST share'              => 'share',  
                        'POST competition-image'  => 'competition-image',  
                        'POST report-post'  => 'report-post', 
                        'POST upload-gallary'  => 'upload-gallary', 
                        'GET promotion-ad-view'      => 'promotion-ad-view',
                        'GET hash-counter-list'      => 'hash-counter-list'
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/collection',
                    'extraPatterns' => [

                        'POST add-post'               => 'add-post',
                        'POST remove-post'               => 'remove-post',
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/highlight',
                    'extraPatterns' => [

                        'POST add-story'               => 'add-story',
                        'POST remove-story'               => 'remove-story',
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/notification',
                    'extraPatterns' => [
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/competition',
                    'extraPatterns' => [
                        'POST join'  => 'join',
                        'GET my-competition'  => 'my-competition',  
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/package',
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/setting',
                    'extraPatterns' => [],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/support-request',
                    'extraPatterns' => [
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/file-upload',
                    'extraPatterns' => [
                        'POST upload-file'       => 'upload-file',
                        
                    ],
                ],
                
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/story',
                    'extraPatterns' => [
                        'GET my-story' => 'my-story',
                        'GET my-active-story' => 'my-active-story'
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/blocked-user',
                    'extraPatterns' => [
                        'POST un-blocked' => 'un-blocked'
                        
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/chat',
                    'extraPatterns' => [
                        'POST create-room'           => 'create-room',
                        'GET room'                   => 'room',
                        'GET room-detail'                   => 'room-detail',
                        'GET delete-room'            => 'delete-room',
                        'POST upload-media-file'     => 'upload-media-file',
                        'GET call-history'           => 'call-history',
                        'POST update-room'           => 'update-room',
                        'GET live-user'              => 'live-user',
                        'GET online-user'             => 'online-user'
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/club',
                    'extraPatterns' => [
                        'GET category'                => 'category',
                        'POST join'                   => 'join',
                        'POST left'                   => 'left',
                        'POST remove'                 => 'remove',
                        'GET club-joined-user'        => 'club-joined-user',
                        
                        
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/live-tv',
                    'extraPatterns' => [
                        'POST subscribe' => 'subscribe',
                        'POST stop-viewing' => 'stop-viewing',
                        'GET my-subscribed-list' => 'my-subscribed-list',
                        'POST add-favorite' => 'add-favorite',
                        'POST remove-favorite' => 'remove-favorite',
                        'GET my-favorite-list' => 'my-favorite-list',

                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/gift',
                    'extraPatterns' => [
                        'POST send-gift' => 'send-gift',
                        'GET recieved-gift' => 'recieved-gift',
                        'GET popular' => 'popular',
                        
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user-live-history',
                    'extraPatterns' => [
                        
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user-verification',
                    'extraPatterns' => [
                        'POST cancel' => 'cancel',
                    ],
                ],
                
               
            ],
        ],      
          /*  
        'response' => [
           
            'format'=>yii\web\Response::FORMAT_JSON,
           
            // ...
          
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ],



      
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule', 
                    'controller' => 'v1/country',
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                    
                    ],
                    [
                        'class' => 'yii\rest\UrlRule', 
                        'controller' => 'v1/photo',
                        'tokens' => [
                            '{id}' => '<id:\\w+>'
                        ]
                        
                    ]
            ],        
        ]*/
    ],
    'params' => $params,
];



