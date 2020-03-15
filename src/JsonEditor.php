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
     * @var mixed this property can be used instead of `value`;
     * while `value` must be JSON string, `decodedValue` accepts decoded JSON, i.e. arrays, floats, booleans etc.;
     * `decodedValue` has precedence over `value`: if `decodedValue` is set then `value` will be ignored
     * @see value
     */
    public $decodedValue;

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
     * @see https://github.com/josdejong/jsoneditor/blob/master/src/docs/which%20files%20do%20I%20need.md
     */
    public $minimalist;

    /**
     * @var string[] list of client options which should be automatically converted to `JsExpression`
     * @see clientOptions
     */
    protected $jsExpressionClientOptions = [
        'ace',
        'ajv',
        'autocomplete',
        'createQuery',
        'executeQuery',
        'languages',
        'modalAnchor',
        'onBlur',
        'onChange',
        'onChangeJSON',
        'onChangeText',
        'onClassName',
        'onColorPicker',
        'onCreateMenu',
        'onEditable',
        'onError',
        'onEvent',
        'onFocus',
        'onModeChange',
        'onNodeName',
        'onSelectionChange',
        'onTextSelectionChange',
        'onValidate',
        'onValidationError',
        'popupAnchor',
        'schema',
        'schemaRefs',
        'templates',
        'timestampFormat',
        'timestampTag',
    ];

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

        $this->determineValue();

        foreach (['mode', 'modes'] as $parameterName) {
            $this->$parameterName = ArrayHelper::getValue($this->clientOptions, $parameterName, $this->$parameterName);
        }
        // make sure that "mode" is specified, otherwise JavaScript error can occur in some situations
        $this->clientOptions['mode'] = $this->mode;

        // if property is not set then try to determine automatically whether full version is needed
        if ($this->minimalist === null) {
            $this->minimalist = $this->mode != 'code' && !in_array('code', $this->modes);
        }
    }

    /**
     * Analyses input data and determines what should be used as value.
     * This method must set `value` and `decodedValue` properties.
     */
    protected function determineValue()
    {
        // decodedValue property has first precedence
        if ($this->decodedValue !== null) {
            $this->value = Json::encode($this->decodedValue);
            return;
        }

        // value property has second precedence
        // options['value'] property has third precedence
        if (!$this->issetValue() && isset($this->options['value'])) {
            $this->value = $this->options['value'];
        }

        // model attribute has fourth precedence
        if (!$this->issetValue() && $this->hasModel()) {
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
        }

        // value is not set anywhere, use default
        if (!$this->issetValue()) {
            $this->value = $this->defaultValue;
        }

        $this->decodedValue = Json::decode($this->value, false);
    }

    /**
     * Check whether `value` property is set. For JSON string the empty string is considered as equivalent of null.
     * @return bool whether `value` property is set.
     */
    protected function issetValue()
    {
        return $this->value !== null && $this->value !== '';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            $this->options['value'] = $this->value; // model may contain decoded JSON, override value for rendering
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
        foreach ($options as $key => $value) {
            if (!$value instanceof JsExpression && in_array($key, $this->jsExpressionClientOptions)) {
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

        $htmlEncodedValue = Json::htmlEncode($this->decodedValue); // Json::htmlEncode is needed to prevent XSS
        $jsCode = "$editorName = new JSONEditor(document.getElementById('{$this->containerOptions['id']}'), " .
            Json::htmlEncode($this->clientOptions) . ");\n" .
            "$editorName.set($htmlEncodedValue);\n" . // have to use set method,
            // because constructor works wrong for '0', 'null', '""'; constructor turns them to {}, which may be wrong
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
