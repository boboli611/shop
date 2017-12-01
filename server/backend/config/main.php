<?php

$params = array_merge(
        require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
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
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => './upload', // 比如这里可以填写 ./uploads
            'uploadUrl' => 'http://admin.ttyouhiu.com/upload',
            'imageAllowExtensions' => ['jpg', 'png', 'gif']
        ],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        "authManager" => [
            "class" => 'yii\rbac\DbManager',
            "defaultRoles" => ["guest"],
        ],
        'Aliyunoss' => [
            'class' => 'backend\components\Aliyunoss',
        ],
        'oss' => [
            'class' => 'backend\components\Oss',
            'accessKeyId' => 'LTAI3nyc5ASNhaih', // 阿里云OSS AccessKeyID
            'accessKeySecret' => 'y8Takqd1V46pH0ubaQGhWvzr98TH3f', // 阿里云OSS AccessKeySecret
            'bucket' => 'lipz', // 阿里云的bucket空间
            'lanDomain' => 'oss-cn-hangzhou.aliyuncs.com', // OSS内网地址  
            'wanDomain' => 'oss-cn-hangzhou.aliyuncs.com', //OSS外网地址
            'isInternal' => true, // 上传文件是否使用内网，免流量费（选填，默认 false 是外网）
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
