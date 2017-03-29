<?php

namespace kdn\yii2;

use kdn\yii2\assets\JsonEditorFullAsset;
use kdn\yii2\assets\JsonEditorMinimalistAsset;
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
            'development' => ['jsoneditor.min.css', 'jsoneditor-minimalist.min.js', 'jsoneditor.min.js'],
        ];
    }

    /**
     * @param string $css
     * @param string $minimalistJs
     * @param string $fullJs
     * @covers       \kdn\yii2\assets\JsonEditorAsset
     * @covers       \kdn\yii2\JsonEditor
     * @dataProvider assetProvider
     * @small
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

        $fullAssetName = JsonEditorFullAsset::className();
        $minimalistAssetName = JsonEditorMinimalistAsset::className();

        $testWidgetAsset(['name' => 'data'], $minimalistAssetName, $css, $minimalistJs);
        static::mockWebApplication();
        $testWidgetAsset(['name' => 'data', 'clientOptions' => ['mode' => 'code']], $fullAssetName, $css, $fullJs);
        static::mockWebApplication();
        $testWidgetAsset(['name' => 'data', 'clientOptions' => ['modes' => ['code']]], $fullAssetName, $css, $fullJs);
    }

    public static function assetProductionProvider()
    {
        return [
            'production' => ['jsoneditor.css', 'jsoneditor-minimalist.js', 'jsoneditor.js'],
        ];
    }

    /**
     * @param string $css
     * @param string $minimalistJs
     * @param string $fullJs
     * @covers       \kdn\yii2\assets\JsonEditorAsset
     * @covers       \kdn\yii2\JsonEditor
     * @dataProvider assetProductionProvider
     * @small
     */
    public function testAssetProduction($css, $minimalistJs, $fullJs)
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
     * @small
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
     * @small
     */
    public function testEditorActiveWidgetAndDefaults()
    {
        $html = static::catchOutput(
            function () {
                $form = ActiveForm::begin(['id' => 'data-form', 'action' => 'test', 'options' => ['csrf' => false]]);
                echo $form->field(new ModelMock, 'data')->widget(JsonEditor::className(), ['expandAll' => ['tree']]);
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
     * @small
     */
    public function testWidgetWithScriptInJson()
    {
        $html = JsonEditor::widget(
            [
                'id' => 'data',
                'name' => 'data',
                'value' => '{"script": "<script type=\"text/javascript\">alert(\"XSS\");</script>"}',
            ]
        );
        $this->assertStringEqualsHtmlFile(__FUNCTION__, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }
}
