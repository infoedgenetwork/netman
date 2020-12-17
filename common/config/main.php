<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'peopledefaults' => [
            'class' => 'common\components\PeopleDefaults',
        ],
        'memberdetails' =>[
            'class' => 'common\components\MemberDetails',
        ],
    ],
];
