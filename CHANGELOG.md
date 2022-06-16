# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.5.1] - 2022-06-16

### Changed

- Running of tests using Docker and documentation for this.

### Fixed

- Fixed minor issue with `clientOptions`: `onExpand`.
  Now there is no need to use `JsExpression` for these options, extension will do it automatically.

## [2.5.0] - 2020-06-17

### Added

- Updated npm-asset/jsoneditor version constraint to allow version 9.

## [2.4.3] - 2020-03-23

### Fixed

- Cannot install package on Windows using Composer because of forbidden characters in some file names.

## [2.4.2] - 2020-03-15

### Fixed

- Fixed minor issue with `clientOptions`: `createQuery` and `executeQuery`.
  Now there is no need to use `JsExpression` for these options, extension will do it automatically.

## [2.4.1] - 2020-03-06

### Added

- Added possibility to run tests using Docker and documentation for this.

## [2.4.0] - 2020-01-12

### Added

- Updated npm-asset/jsoneditor version constraint to allow version 8.

### Fixed

- Fixed minor issue with `clientOptions`: `onBlur`, `onFocus`, `onValidationError`, `popupAnchor`,
  `timestampFormat` and `timestampTag`.
  Now there is no need to use `JsExpression` for these options, extension will do it automatically.

## [2.3.0] - 2019-11-01

### Added

- Added optional property `decodedValue`, this property can be used instead of `value`.
  While `value` must be JSON string, `decodedValue` accepts decoded JSON, i.e. arrays, floats, booleans, etc.
  `decodedValue` has precedence over `value`: if `decodedValue` is set then `value` will be ignored.

### Fixed

- \#9: Allow overriding of model attribute value using explicit setting of `value` property for widget.
- \#8: Allow overriding of model attribute value using options['inputOptions']['value'].
- \#7: Strings '0', 'null' and '""' are valid JSON and should not be automatically replaced on {} or `defaultValue`.
- \#6: Default value is ignored by "value" attribute of hidden input when Model is used.
- Fixed minor issue with `clientOptions`: `autocomplete`, `languages`, `modalAnchor`, `onChange`, `onChangeJSON`,
  `onChangeText`, `onClassName`, `onColorPicker`, `onCreateMenu`, `onEditable`, `onError`, `onEvent`, `onModeChange`,
  `onNodeName`, `onSelectionChange`, `onTextSelectionChange`, `onValidate`, `schemaRefs` and `templates`.
  Now there is no need to use `JsExpression` for these options, extension will do it automatically.

## [2.2.0] - 2019-09-08

### Added

- Updated npm-asset/jsoneditor version constraint to allow version 7.

## [2.1.0] - 2019-06-21

### Added

- Updated npm-asset/jsoneditor version constraint to allow version 6.

## [2.0.0] - 2018-07-27

### Changed

- Removed inline style `height: 250px;` to simplify specification of custom height.
  This change may affect your design. You can specify custom height like this:

  ```css
  div.jsoneditor {
      height: 250px;
  }
  ```

## [1.0.4] 2017-09-28

### Fixed

- \#3: Widget doesn't work for tabular data input (multiple models in form).

## [1.0.3] - 2017-03-29

### Fixed

- \#2: Widget doesn't work if JSON contains `<script>` tag.

## [1.0.2] - 2016-08-27

### Fixed

- Fixed minor issue with `clientOptions`: `ace`, `ajv` and `schema`.
  Now there is no need to use `JsExpression` for these options, extension will do it automatically.

## [1.0.1] - 2016-08-25

### Added

- Documentation. Minor optimization of generated JavaScript.

## [1.0.0] - 2016-08-24

- Initial release.
