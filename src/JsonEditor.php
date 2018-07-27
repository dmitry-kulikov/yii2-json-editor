<?php

namespace kdn\yii2;

use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * Class JsonEditor.
 * @package kdn\yii2
 */
class JsonEditor extends InputWidget
{
    /**
     * @var array options which will be passed to JSON editor
     * @see https://github.com/josdejong/jsoneditor/blob/master/docs/api.md#configuration-options
     */
    public $clientOptions = [];

    /**
     * @var string[] list of JSON editor modes for which all fields should be collapsed automatically;
     * allowed modes 'tree', 'view', and 'form'
     * @see https://github.com/josdejong/jsoneditor/blob/master/docs/api.md#jsoneditorcollapseall
     */
    public $collapseAll = [];

    /**
     * @var array HTML attributes to be applied to the JSON editor container tag
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered
     */
    public $containerOptions = [];

    /**
     * @var string default value
     */
    public $defaultValue = '{}';

    /**
     * @var string[] list of JSON editor modes for which all fields should be expanded automatically;
     * allowed modes 'tree', 'view', and 'form'
     * @see https://github.com/josdejong/jsoneditor/blob/master/docs/api.md#jsoneditorexpandall
     */
    public $expandAll = [];

    /**
     * @var null|bool whether to use minimalist version of JSON editor;
     * note that "minimalist" is not the same as "minimized";
     * if property is not set then extension will try to determine automatically whether full version is needed,
     * if full version is not required then minimalist version will be used;
     * you can explicitly set this property to true or false if automatic detection does not fit for you application
     * @see https://github.com/josdejong/jsoneditor/blob/master/dist/which%20files%20do%20I%20need.md
     */
    public $minimalist;

    /**
     * @var string default JSON editor mode
     */
    private $mode = 'tree';

    /**
     * @var string[] available JSON editor modes
     */
    private $modes = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if (!isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = $this->options['id'] . '-json-editor';
        }
        if ($this->hasModel()) {
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
        }
        if (empty($this->value)) {
            $this->value = $this->defaultValue;
        }
        foreach (['mode', 'modes'] as $parameterName) {
            $this->$parameterName = ArrayHelper::getValue($this->clientOptions, $parameterName, $this->$parameterName);
        }
        // make sure that "mode" is specified, otherwise JavaScript error can occur in some situations
        $this->clientOptions['mode'] = $this->mode;
        if (!isset($this->minimalist)) {
            $this->minimalist = $this->mode != 'code' && !in_array('code', $this->modes);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            echo Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::hiddenInput($this->name, $this->value, $this->options);
        }
        echo Html::tag('div', '', $this->containerOptions);
    }

    /**
     * Initializes client options.
     */
    protected function initClientOptions()
    {
        $options = $this->clientOptions;
        $jsExpressionOptions = ['ace', 'ajv', 'onChange', 'onEditable', 'onError', 'onModeChange', 'schema'];
        foreach ($options as $key => $value) {
            if (!$value instanceof JsExpression && in_array($key, $jsExpressionOptions)) {
                $options[$key] = new JsExpression($value);
            }
        }
        $this->clientOptions = $options;
    }

    /**
     * Registers the needed client script.
     */
    public function registerClientScript()
    {
        $this->initClientOptions();
        $view = $this->getView();

        if ($this->minimalist) {
            JsonEditorMinimalistAsset::register($view);
        } else {
            JsonEditorFullAsset::register($view);
        }

        $hiddenInputId = $this->options['id'];
        $editorName = Inflector::variablize($hiddenInputId) . 'JsonEditor_' . hash('crc32', $hiddenInputId);
        $this->options['data-json-editor-name'] = $editorName;

        $jsUpdateHiddenField = "jQuery('#$hiddenInputId').val($editorName.getText());";

        if (isset($this->clientOptions['onChange'])) {
            $userFunction = " var userFunction = {$this->clientOptions['onChange']}; userFunction.call(this);";
        } else {
            $userFunction = '';
        }
        $this->clientOptions['onChange'] = new JsExpression("function() {{$jsUpdateHiddenField}$userFunction}");

        if (!empty($this->collapseAll) || !empty($this->expandAll)) {
            if (isset($this->clientOptions['onModeChange'])) {
                $userFunction = " var userFunction = {$this->clientOptions['onModeChange']}; " .
                    "userFunction.call(this, newMode, oldMode);";
            } else {
                $userFunction = '';
            }
            $jsOnModeChange = "function(newMode, oldMode) {";
            foreach (['collapseAll', 'expandAll'] as $property) {
                if (!empty($this->$property)) {
                    $jsOnModeChange .= "if (" . Json::htmlEncode($this->$property) . ".indexOf(newMode) !== -1) " .
                        "{{$editorName}.$property();}";
                }
            }
            $jsOnModeChange .= "$userFunction}";
            $this->clientOptions['onModeChange'] = new JsExpression($jsOnModeChange);
        }

        $encodedValue = Json::htmlEncode(Json::decode($this->value, false));
        $jsCode = "$editorName = new JSONEditor(document.getElementById('{$this->containerOptions['id']}'), " .
            Json::htmlEncode($this->clientOptions) . ", $encodedValue);\n" .
            "jQuery('#$hiddenInputId').parents('form').submit(function() {{$jsUpdateHiddenField}});";
        if (in_array($this->mode, $this->collapseAll)) {
            $jsCode .= "\n$editorName.collapseAll();";
        }
        if (in_array($this->mode, $this->expandAll)) {
            $jsCode .= "\n$editorName.expandAll();";
        }
        $view->registerJs($jsCode);
    }
}
