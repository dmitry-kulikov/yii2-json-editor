<?php

namespace kdn\yii2\assets;

/**
 * Class JsonEditorFullAsset.
 * @package kdn\yii2\assets
 */
class JsonEditorFullAsset extends JsonEditorAsset
{
    /**
     * {@inheritdoc}
     */
    public $jsDev = [
        'jsoneditor.js',
    ];

    /**
     * {@inheritdoc}
     */
    public $jsProd = [
        'jsoneditor.min.js',
    ];
}
