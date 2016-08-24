JSON editor widget for Yii 2
============================

This extension provides the [JSON editor](http://jsoneditoronline.org/) integration for the Yii2 framework.


Installation
------------

This extension requires [JSON editor](https://github.com/josdejong/jsoneditor/)

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist kdn/yii2-json-editor "*"
```

or add

```
"kdn/yii2-json-editor": "*"
```

to the require section of your composer.json.


General Usage
-------------

```php
use kdn\yii2\JsonEditor;

JsonEditor::widget(
    [
        'clientOptions' => [
            'modes' => ['code', 'form', 'text', 'tree', 'view'], // available modes
            'mode' => 'tree', // default mode
        ],
        'name' => 'editor',
        'options' => [], // html options
    ]
);
```

or with active form

```php
use kdn\yii2\JsonEditor;

$form->field($model, 'name')->widget(
    JsonEditor::className(),
    [
        'clientOptions' => [
            'modes' => ['code', 'form', 'text', 'tree', 'view'], // available modes
            'mode' => 'tree', // default mode
        ],
    ]
);
```
