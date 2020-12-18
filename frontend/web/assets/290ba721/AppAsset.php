<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    /* public $sourcePath='@app/assets';*/
    public $css = [
        'css/site.css',
        'css/local.css',
        [
            'href' => 'images/kte_device.ico',
            'rel' => 'icon',
            'sizes' => '32x32',
        ],
        [
            'background-image'=>'images/bckground.jpg',
            'alt'  => 'bckground'
        ],
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
