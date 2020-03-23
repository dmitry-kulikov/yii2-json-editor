<?php

namespace kdn\yii2;

use kdn\yii2\mocks\ModelMock;
use Yii;
use yii\widgets\ActiveForm;

/**
 * Class JsonEditorTest.
 * @package kdn\yii2
 */
class JsonEditorTest extends TestCase
{
    public static function assetProvider()
    {
        return [
            'production' => ['jsoneditor.min.css', 'jsoneditor-minimalist.min.js', 'jsoneditor.min.js'],
        ];
    }

    /**
     * @param string $css
     * @param string $minimalistJs
     * @param string $fullJs
     * @covers       \kdn\yii2\assets\JsonEditorAsset
     * @covers       \kdn\yii2\JsonEditor
     * @dataProvider assetProvider
     * @medium
     */
    public function testAsset($css, $minimalistJs, $fullJs)
    {
        $testWidgetAsset = function ($config, $assetName, $css, $js) {
            JsonEditor::widget($config);
            $bundles = Yii::$app->assetManager->bundles;
            $this->assertArrayHasKey($assetName, $bundles);
            $this->assertEquals([$css], $bundles[$assetName]->css);
            $this->assertEquals([$js], $bundles[$assetName]->js);
        };

        $fullAssetName = 'kdn\yii2\assets\JsonEditorFullAsset';
        $minimalistAssetName = 'kdn\yii2\assets\JsonEditorMinimalistAsset';

        $testWidgetAsset(['name' => 'data'], $minimalistAssetName, $css, $minimalistJs);
        static::mockWebApplication();
        $testWidgetAsset(['name' => 'data', 'clientOptions' => ['mode' => 'code']], $fullAssetName, $css, $fullJs);
        static::mockWebApplication();
        $testWidgetAsset(['name' => 'data', 'clientOptions' => ['modes' => ['code']]], $fullAssetName, $css, $fullJs);
    }

    public static function assetDevelopmentProvider()
    {
        return [
            'development' => ['jsoneditor.css', 'jsoneditor-minimalist.js', 'jsoneditor.js'],
        ];
    }

    /**
     * @param string $css
     * @param string $minimalistJs
     * @param string $fullJs
     * @covers       \kdn\yii2\assets\JsonEditorAsset
     * @covers       \kdn\yii2\JsonEditor
     * @dataProvider assetDevelopmentProvider
     * @medium
     */
    public function testAssetDevelopment($css, $minimalistJs, $fullJs)
    {
        if (!function_exists('runkit_constant_redefine')) {
            $this->markTestSkipped('runkit extension required.');
            return;
        }

        $yiiEnvDev = YII_ENV_DEV;
        runkit_constant_redefine('YII_ENV_DEV', true);
        $this->testAsset($css, $minimalistJs, $fullJs);
        runkit_constant_redefine('YII_ENV_DEV', $yiiEnvDev);
    }

    /**
     * @covers \kdn\yii2\JsonEditor
     * @uses   \kdn\yii2\assets\JsonEditorAsset
     * @medium
     */
    public function testEditorWidget()
    {
        $html = JsonEditor::widget(
            [
                'clientOptions' => [
                    'modes' => ['code', 'form', 'preview', 'text', 'tree', 'view'],
                    'mode' => 'view',
                    'onChange' => 'function () {console.log(this);}',
                    'onError' => 'function (error) {console.log(error);}',
                    'onModeChange' => 'function (nMode, oMode) {console.log(this, nMode, oMode);}',
                ],
                'collapseAll' => ['view'],
                'containerOptions' => ['class' => 'container'],
                'expandAll' => ['tree', 'form'],
                'name' => 'data',
                'options' => ['id' => 'data'],
            ]
        );
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }

    /**
     * @covers \kdn\yii2\JsonEditor
     * @uses   \kdn\yii2\assets\JsonEditorAsset
     * @medium
     */
    public function testEditorWidgetWithScriptInJson()
    {
        $html = JsonEditor::widget(
            [
                'id' => 'data',
                'name' => 'data',
                'value' => '{"script":"<script>alert(\"XSS\");</script>"}',
            ]
        );
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }

    public static function editorWidgetAndDefaultValueProvider()
    {
        return [
            '0' => ['0', '0'],
            'null' => ['null', 'null'],
            '""' => ['""', 'empty_json_string'],
            'empty string' => ['', 'default'],
        ];
    }

    /**
     * @param string $value
     * @param string $expectedResult
     * @covers       \kdn\yii2\JsonEditor
     * @uses         \kdn\yii2\assets\JsonEditorAsset
     * @dataProvider editorWidgetAndDefaultValueProvider
     * @medium
     */
    public function testEditorWidgetAndDefaultValue($value, $expectedResult)
    {
        $html = JsonEditor::widget(
            ['id' => 'data', 'name' => 'data', 'value' => $value, 'defaultValue' => '{"foo":"bar"}']
        );
        $fileRoot = __FUNCTION__ . '_' . $expectedResult;
        $this->assertStringEqualsHtmlFile($fileRoot, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile($fileRoot, reset($jsCodeBlock));
    }

    /**
     * @covers \kdn\yii2\JsonEditor
     * @uses   \kdn\yii2\assets\JsonEditorAsset
     * @medium
     */
    public function testEditorActiveWidgetAndDefaults()
    {
        $html = OutputHelper::catchOutput(
            function () {
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field(new ModelMock(), 'data')->widget(
                    'kdn\yii2\JsonEditor',
                    ['expandAll' => ['tree'], 'options' => ['class' => false]]
                );
                ActiveForm::end();
            }
        )['output'];
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }

    /**
     * @covers \kdn\yii2\JsonEditor
     * @uses   \kdn\yii2\assets\JsonEditorAsset
     * @medium
     */
    public function testEditorActiveWidgetWithAttributeExpression()
    {
        $html = OutputHelper::catchOutput(
            function () {
                $model = new ModelMock();
                $model->data = ['{}', '{"foo":"bar"}'];
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field($model, '[1]data[1]')->widget(
                    'kdn\yii2\JsonEditor',
                    ['options' => ['class' => false]]
                );
                ActiveForm::end();
            }
        )['output'];
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }

    public static function editorActiveWidgetPrecedenceProvider()
    {
        return [
            'decoded value' => [
                ['precedence' => 1],
                '{"precedence":2}',
                '{"precedence":3}',
                '{"precedence":4}',
                '{"precedence":5}',
                1,
            ],
            'value' => [
                null,
                '{"precedence":2}',
                '{"precedence":3}',
                '{"precedence":4}',
                '{"precedence":5}',
                2,
            ],
            "options['inputOptions']['value']" => [
                null,
                '',
                '{"precedence":3}',
                '{"precedence":4}',
                '{"precedence":5}',
                3,
            ],
            'model data' => [
                null,
                '',
                null,
                '{"precedence":4}',
                '{"precedence":5}',
                4,
            ],
            'default value' => [
                null,
                '',
                null,
                null,
                '{"precedence":5}',
                5,
            ],
        ];
    }

    /**
     * @param mixed $decodedValue
     * @param null|string $value
     * @param null|string $inputOptionsValue
     * @param null|string $modelData
     * @param null|string $defaultValue
     * @param int $expectedResult
     * @covers       \kdn\yii2\JsonEditor
     * @uses         \kdn\yii2\assets\JsonEditorAsset
     * @dataProvider editorActiveWidgetPrecedenceProvider
     * @medium
     */
    public function testEditorActiveWidgetPrecedence(
        $decodedValue,
        $value,
        $inputOptionsValue,
        $modelData,
        $defaultValue,
        $expectedResult
    ) {
        $html = OutputHelper::catchOutput(
            function () use ($decodedValue, $value, $inputOptionsValue, $modelData, $defaultValue) {
                $model = new ModelMock();
                $model->data = ['{"foo":"bar"}', $modelData];
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field($model, '[1]data[1]', ['inputOptions' => ['value' => $inputOptionsValue]])->widget(
                    'kdn\yii2\JsonEditor',
                    [
                        'decodedValue' => $decodedValue,
                        'value' => $value,
                        'defaultValue' => $defaultValue,
                        'options' => ['class' => false],
                    ]
                );
                ActiveForm::end();
            }
        )['output'];
        $fileRoot = __FUNCTION__ . '_' . $expectedResult;
        $this->assertStringEqualsHtmlFile($fileRoot, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile($fileRoot, reset($jsCodeBlock));
    }
}
