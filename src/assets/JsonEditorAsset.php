<?php

namespace kdn\yii2\assets;

use yii\web\AssetBundle;

/**
 * Class JsonEditorAsset.
 * @package kdn\yii2\assets
 */
abstract class JsonEditorAsset extends AssetBundle
{
    /**
     * @var array list of CSS files which this bundle will use for development environment
     * @see $css
     */
    public $cssDev = [
        'jsoneditor.css',
    ];

    /**
     * @var array list of CSS files which this bundle will use for production environment
     * @see $css
     */
    public $cssProd = [
        'jsoneditor.min.css',
    ];

    /**
     * @var array list of JavaScript files which this bundle will use for development environment
     * @see $js
     */
    public $jsDev;

    /**
     * @var array list of JavaScript files which this bundle will use for production environment
     * @see $js
     */
    public $jsProd;

    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@npm/jsoneditor/dist';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (empty($this->css)) {
            if (YII_ENV_DEV) {
                $this->css = $this->cssDev;
            } else {
                $this->css = $this->cssProd;
            }
        }
        if (empty($this->js)) {
            if (YII_ENV_DEV) {
                $this->js = $this->jsDev;
            } else {
                $this->js = $this->jsProd;
            }
        }
        parent::init();
    }
}
