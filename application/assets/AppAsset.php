<?php

namespace application\assets;

use yii\web\AssetBundle;

/**
 * Main application application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.css',
        'css/chosen.min.css',
        'css/jquery-ui.min.css',
        'fonts/font-awesome-4.7.0/css/font-awesome.min.css',
        'css/ace.min.css',
        'css/custom-style.css'

    ];
    public $js = [
        'js/ace-extra.min.js',
        'js/jquery.number.js',
        'js/customFunction.js',
        'js/bootstrap.min.js',
        'js/jquery-ui.min.js',
        'js/ace.min.js',
        'js/chosen.jquery.min.js',
        'js/inputmask/jquery.inputmask.bundle.min.js',
        'js/inputmask/inputmask/inputmask.date.extensions.min.js',
        'js/custom-site-js.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
