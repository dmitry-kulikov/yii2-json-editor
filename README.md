# yii2-json-editor

[JSON editor](https://jsoneditoronline.org) widget for Yii 2.  
This widget uses JSON editor [josdejong/jsoneditor](https://github.com/josdejong/jsoneditor).

[![License](https://poser.pugx.org/kdn/yii2-json-editor/license)](https://packagist.org/packages/kdn/yii2-json-editor)
[![Latest Stable Version](https://poser.pugx.org/kdn/yii2-json-editor/v/stable)](https://packagist.org/packages/kdn/yii2-json-editor)
[![Build Status](https://travis-ci.org/dmitry-kulikov/yii2-json-editor.svg?branch=master)](https://travis-ci.org/dmitry-kulikov/yii2-json-editor)
[![Code Coverage](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/?branch=master)
[![Code Climate](https://codeclimate.com/github/dmitry-kulikov/yii2-json-editor/badges/gpa.svg)](https://codeclimate.com/github/dmitry-kulikov/yii2-json-editor)

# Requirements

- PHP 5.4 or later or HHVM 3;
- Yii framework 2.

# Installation

The preferred way to install this extension is through [Composer](https://getcomposer.org).

To install, either run

```
php composer.phar require kdn/yii2-json-editor "*"
```

or add

```
"kdn/yii2-json-editor": "*"
```

to the `require` section of your `composer.json` file.

# Usage

Minimal example:

```php
<?php

use kdn\yii2\JsonEditor;

echo JsonEditor::widget(['name' => 'editor', 'value' => '{"foo": "bar"}']);
```

Alternatively you can pass already decoded JSON:

```php
<?php

use kdn\yii2\JsonEditor;

echo JsonEditor::widget(['name' => 'editor', 'decodedValue' => ['foo' => 'bar']]);
```

With some options:

```php
echo JsonEditor::widget(
    [
        // JSON editor options
        'clientOptions' => [
            'modes' => ['code', 'form', 'preview', 'text', 'tree', 'view'], // all available modes
            'mode' => 'tree', // default mode
            'onModeChange' => 'function (newMode, oldMode) {
                console.log(this, newMode, oldMode);
            }',
        ],
        'collapseAll' => ['view'], // collapse all fields in "view" mode
        'containerOptions' => ['class' => 'container'], // HTML options for JSON editor container tag
        'expandAll' => ['tree', 'form'], // expand all fields in "tree" and "form" modes
        'name' => 'editor', // hidden input name
        'options' => ['id' => 'data'], // HTML options for hidden input
        'value' => '{"foo": "bar"}', // JSON which should be shown in editor
    ]
);
```

With ActiveForm and ActiveRecord:

```php
echo $form->field($model, 'data')->widget(
    JsonEditor::class,
    [
        'clientOptions' => ['modes' => ['code', 'tree']],
        'decodedValue' => $model->data, /* if attribute contains already decoded JSON,
        then you should pass it as shown, otherwise omit this line */
    ]
);
```

To get instance of JSON editor on client side you can use the following JavaScript:

```javascript
var jsonEditor = window[$('#YOUR-HIDDEN-INPUT-ID').data('json-editor-name')];
jsonEditor.set({"foo": "bar"});
```

How to set `id` for hidden input:

```php
echo JsonEditor::widget(
    [
        'name' => 'editor',
        'options' => ['id' => 'YOUR-HIDDEN-INPUT-ID'],
        'value' => '{}'
    ]
);
```

All possible ways to pass data and their precedence:

```php
$model->data = '{"precedence": 5}';
echo $form->field(
    $model,
    'data',
    ['inputOptions' => ['value' => '{"precedence": 4}']]
)->widget(
    'kdn\yii2\JsonEditor',
    [
        'decodedValue' => ['precedence' => 1],
        'value' => '{"precedence": 2}',
        'options' => ['value' => '{"precedence": 3}'],
        'defaultValue' => '{"precedence": 6}',
    ]
);
```

For code above widget will show `{"precedence": 1}`.  
If `decodedValue` is not set then widget will show `{"precedence": 2}` etc.

Please view public properties in class
[JsonEditor](https://github.com/dmitry-kulikov/yii2-json-editor/blob/master/src/JsonEditor.php)
to get info about all available options, they documented comprehensively.

# Testing

Make sure you installed all composer dependencies (run `composer update` in the base directory of repository).
Run PHPUnit in the base directory of repository:

```sh
./vendor/bin/phpunit
```

## Testing using Docker

#### Requirements

- Docker 18.09.0 or later ([install](https://docs.docker.com/install));
- Docker Compose 1.22.0 or later ([install](https://docs.docker.com/compose/install));
- Docker plugins:
  - `buildx` ([install](https://github.com/docker/buildx#installing)).

#### Up and running

Provide credentials to composer:

```sh
cp tests/composer/auth.json.example tests/composer/auth.json
```

I suggest to set GitHub OAuth token (also known as personal access token) in `tests/composer/auth.json`,
however if you have doubts about security or you are lazy to generate token then you can replace content of
`auth.json` on `{}`, in most cases this will work.

Build image for service:

```sh
docker buildx bake --pull 7.4
```

This command will build image using PHP 7.4. Also allowed `7.4-alpine`, `5.6`, `5.6-alpine` and others, see services
defined in `docker-compose.yml`.

Start service in background mode:

```sh
docker-compose up --detach 7.4
```

Execute tests in the running container:

```sh
docker-compose exec 7.4 ./vendor/bin/phpunit
```

Alternatively you can start shell in the running container and execute tests from it:

```sh
docker-compose exec 7.4 sh
$ ./vendor/bin/phpunit
```

Update composer dependencies in the running container:

```sh
docker-compose exec 7.4 sh
$ ./tests/composer/update-dependencies.sh
```

Stop and remove containers created by `up`:

```sh
docker-compose down
```

You may want to remove volumes along with containers:

```sh
docker-compose down --volume
```
