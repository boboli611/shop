<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
   
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
	"admin" => [
        	"class" => "mdm\admin\Module",
    	],
        'redactor' => 'yii\redactor\RedactorModule',
	
    ],
    "aliases" => [
    	"@mdm/admin" => "@vendor/mdmsoft/yii2-admin",
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\UserBackend',
            'enableAutoLogin' => true,
            //'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
<<<<<<< HEAD
         * */
=======
         
>>>>>>> 2de1984676d95e3a540ddb385d06936e39c76181
	 "authManager" => [
                "class" => 'yii\rbac\DbManager',
                "defaultRoles" => ["guest"],
        ],
        
    ],
    'as access' => [
    	'class' => 'mdm\admin\components\AccessControl',
    	'allowActions' => [
        	//这里是允许访问的action
        	//controller/action
    		// * 表示允许所有，后期会介绍这个
        	'*'
    	]
    ],
    
    'params' => $params,
];
