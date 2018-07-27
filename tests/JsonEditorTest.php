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
                    'modes' => ['code', 'form', 'text', 'tree', 'view'],
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
                'value' => '{"script": "<script>alert(\"XSS\");</script>"}',
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
    public function testEditorActiveWidgetAndDefaults()
    {
        $html = static::catchOutput(
            function () {
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field(new ModelMock, 'data')->widget('kdn\yii2\JsonEditor', ['expandAll' => ['tree']]);
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
        $html = static::catchOutput(
            function () {
                $model = new ModelMock;
                $model->data = ['{}', '{"foo": "bar"}'];
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field($model, '[1]data[1]')->widget('kdn\yii2\JsonEditor');
                ActiveForm::end();
            }
        )['output'];
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }
}
