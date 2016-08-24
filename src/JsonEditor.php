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
     * @var array JSON editor options
     * @see https://github.com/josdejong/jsoneditor
     */
    public $clientOptions = [];

    /**
     * @var string[] list of JSON editor modes for which all fields should be collapsed automatically;
     * allowed modes 'tree', 'view', and 'form'
     * @see https://github.com/josdejong/jsoneditor
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
     * @see https://github.com/josdejong/jsoneditor
     */
    public $expandAll = [];

    /**
     * @var null|boolean whether to use minimalist version of JSON editor;
     * note that "minimalist" is not the same as "minimized"
     * @see https://github.com/josdejong/jsoneditor
     */
    public $minimalist;

    /**
     * @var string default JSON editor mode
     */
    protected $mode = 'tree';

    /**
     * @var string[] available JSON editor modes
     */
    protected $modes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = $this->options['id'] . '-json-editor';
        }
        if (!array_key_exists('style', $this->containerOptions)) {
            $this->containerOptions['style'] = 'height: 250px;';
        }
        if ($this->hasModel()) {
            if (empty($this->model->{$this->attribute})) {
                $this->model->{$this->attribute} = $this->defaultValue;
            }
        } else {
            if (empty($this->value)) {
                $this->value = $this->defaultValue;
            }
        }
        foreach (['mode', 'modes'] as $parameterName) {
            $this->$parameterName = ArrayHelper::getValue($this->clientOptions, $parameterName, $this->$parameterName);
        }
        $this->clientOptions['mode'] = $this->mode; // make sure that "mode" is specified, otherwise JS error can occur
        if (!isset($this->minimalist)) {
            $this->minimalist = $this->mode != 'code' && !in_array('code', $this->modes);
        }
    }

    /**
     * @inheritdoc
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
        $jsExpressionOptions = ['onChange', 'onEditable', 'onError', 'onModeChange'];
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
            $this->clientOptions['onModeChange'] = new JsExpression(
                "function(newMode, oldMode) {" .
                "if (" . Json::htmlEncode($this->collapseAll) . ".indexOf(newMode) != -1) " .
                "{{$editorName}.collapseAll();} " .
                "if (" . Json::htmlEncode($this->expandAll) . ".indexOf(newMode) != -1) " .
                "{{$editorName}.expandAll();}$userFunction}"
            );
        }

        if ($this->hasModel()) {
            $value = $this->model->{$this->attribute};
        } else {
            $value = $this->value;
        }
        $js = "$editorName = new JSONEditor(document.getElementById('{$this->containerOptions['id']}'), " .
            Json::htmlEncode($this->clientOptions) . ", $value);\n" .
            "jQuery('#$hiddenInputId').parents('form').submit(function() {{$jsUpdateHiddenField}});";
        if (in_array($this->mode, $this->collapseAll)) {
            $js .= "\n$editorName.collapseAll();";
        }
        if (in_array($this->mode, $this->expandAll)) {
            $js .= "\n$editorName.expandAll();";
        }
        $view->registerJs($js);
    }
}
