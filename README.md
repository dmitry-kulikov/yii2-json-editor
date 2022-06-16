# yii2-json-editor

[JSON editor](https://jsoneditoronline.org) widget for Yii 2.  
This widget uses JSON editor [josdejong/jsoneditor](https://github.com/josdejong/jsoneditor).

[![License](https://poser.pugx.org/kdn/yii2-json-editor/license)](https://packagist.org/packages/kdn/yii2-json-editor)
[![Latest Stable Version](https://poser.pugx.org/kdn/yii2-json-editor/v/stable)](https://packagist.org/packages/kdn/yii2-json-editor)
[![Code Coverage](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dmitry-kulikov/yii2-json-editor/?branch=master)
[![Code Climate](https://codeclimate.com/github/dmitry-kulikov/yii2-json-editor/badges/gpa.svg)](https://codeclimate.com/github/dmitry-kulikov/yii2-json-editor)

## Requirements

- PHP 5.4 or later or HHVM 3;
- Yii framework 2.

## Installation

The preferred way to install this extension is through [Composer](https://getcomposer.org).

To install, either run

```sh
php composer.phar require kdn/yii2-json-editor "*"
```

or add

```text
"kdn/yii2-json-editor": "*"
```

to the `require` section of your `composer.json` file.

## Usage

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
    JsonEditor::class,
    [
        'decodedValue' => ['precedence' => 1],
        'value' => '{"precedence": 2}',
        'options' => ['value' => '{"precedence": 3}'],
        'defaultValue' => '{"precedence": 6}',
    ]
);
```

For code above widget will show `{"precedence": 1}`.  
If `decodedValue` is not set then widget will show `{"precedence": 2}`, etc.

Please view public properties in class
[JsonEditor](https://github.com/dmitry-kulikov/yii2-json-editor/blob/master/src/JsonEditor.php)
to get info about all available options, they documented comprehensively.

## Testing

Make sure you installed all Composer dependencies (run `composer update` in the base directory of repository).
Run PHPUnit in the base directory of repository:

```sh
./vendor/bin/phpunit
```

### Testing using Docker

#### Requirements

- Docker >= 19.03.0 ([install](https://docs.docker.com/get-docker));
- Docker Compose >= 1.25.5 ([install](https://docs.docker.com/compose/install));
- Docker plugins:
  - buildx ([install](https://github.com/docker/buildx#installing)).

#### Up and running

1. Provide credentials for Composer:

   ```sh
   cp auth.json.example \
       auth.json
   ```

   I suggest to set GitHub OAuth token (also known as personal access token) in `auth.json`,
   however if you have doubts about security, or you are lazy to generate token then you can replace content of
   `auth.json` on `{}`, in most cases this will work.

1. Build images for services:

   ```sh
   docker buildx bake --load --pull
   ```

   or

   ```sh
   docker buildx bake --load --pull --no-cache --progress plain
   ```

   see `docker buildx bake --help` for details.

1. Start service in background mode:

   ```sh
   docker-compose up --detach 8.1
   ```

   This command will start the service with PHP 8.1. Also allowed `7.4`, `5.6`, `8.1-alpine`, `7.4-alpine`
   and `5.6-alpine`, see services defined in `docker-compose.yml`.

1. Execute tests in the running container:

   ```sh
   docker-compose exec 8.1 ./vendor/bin/phpunit
   ```

   Alternatively you can start a shell in the running container and execute tests from it:

   ```sh
   docker-compose exec 8.1 sh
   $ ./vendor/bin/phpunit
   ```

1. Stop and remove containers created by `up`:

   ```sh
   docker-compose down
   ```

   You may want to remove volumes along with containers:

   ```sh
   docker-compose down --volumes
   ```

## Backward compatibility promise

yii2-json-editor is using [Semver](https://semver.org). This means that versions are tagged
with MAJOR.MINOR.PATCH. Only a new major version will be allowed to break backward
compatibility (BC).

PHP 8 introduced [named arguments](https://wiki.php.net/rfc/named_params), which
increased the cost and reduces flexibility for package maintainers. The names of the
arguments for methods in yii2-json-editor is not included in our BC promise.
