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
 * @covers kdn\yii2\assets\JsonEditorAsset
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
     * @covers       kdn\yii2\JsonEditor
     * @dataProvider assetProvider
     * @small
     */
    public function testAsset($css, $minimalistJs, $fullJs)
    {
        JsonEditor::widget(['name' => 'data']);
        $bundles = Yii::$app->assetManager->bundles;
        $assetName = JsonEditorMinimalistAsset::className();
        $this->assertArrayHasKey($assetName, $bundles);
        $this->assertEquals([$css], $bundles[$assetName]->css);
        $this->assertEquals([$minimalistJs], $bundles[$assetName]->js);

        JsonEditor::widget(['name' => 'data', 'clientOptions' => ['mode' => 'code']]);
        $bundles = Yii::$app->assetManager->bundles;
        $assetName = JsonEditorFullAsset::className();
        $this->assertArrayHasKey($assetName, $bundles);
        $this->assertEquals([$css], $bundles[$assetName]->css);
        $this->assertEquals([$fullJs], $bundles[$assetName]->js);
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
     * @covers       kdn\yii2\JsonEditor
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
     * @covers kdn\yii2\JsonEditor
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
        $expectedHtml = '<input type="hidden" id="data" name="data" value="{}" ' .
            'data-json-editor-name="dataJsonEditor_729ee6af">' .
            '<div id="data-json-editor" class="container" style="height: 250px;"></div>';
        $this->assertEquals($expectedHtml, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }

    /**
     * @covers kdn\yii2\JsonEditor
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
        $expectedHtml = '<form id="data-form" action="test" method="post">' .
            '<div class="form-group field-modelmock-data">' . "\n" .
            '<label class="control-label" for="modelmock-data">Data</label>' . "\n" .
            '<input type="hidden" id="modelmock-data" name="ModelMock[data]" ' .
            'value="{}" data-json-editor-name="modelmockDataJsonEditor_cb242086">' .
            '<div id="modelmock-data-json-editor" style="height: 250px;"></div>' . "\n\n" .
            '<div class="help-block"></div>' . "\n" .
            '</div>' .
            '</form>';
        $this->assertEquals($expectedHtml, $html);
        $jsCodeBlock = reset(Yii::$app->view->js);
        $this->assertStringEqualsJsFile(__FUNCTION__, reset($jsCodeBlock));
    }
}
