<?php

namespace kdn\yii2\assets;

/**
 * Class JsonEditorMinimalistAsset.
 * @package kdn\yii2\assets
 */
class JsonEditorMinimalistAsset extends JsonEditorAsset
{
    /**
     * {@inheritdoc}
     */
    public $jsDev = [
        'jsoneditor-minimalist.js',
    ];

    /**
     * {@inheritdoc}
     */
    public $jsProd = [
        'jsoneditor-minimalist.min.js',
    ];
}
