Change Log
==========

2.3.0 November 1, 2019
----------------------

- Enh: added optional property `decodedValue`, this property can be used instead of `value`.
While `value` must be JSON string, `decodedValue` accepts decoded JSON, i.e. arrays, floats, booleans etc.
`decodedValue` has precedence over `value`: if `decodedValue` is set then `value` will be ignored.
- Bug #9: Allow overriding of model attribute value using explicit setting of `value` property for widget.
- Bug #8: Allow overriding of model attribute value using options['inputOptions']['value'].
- Bug #7: Strings '0', 'null' and '""' are valid JSON and should not be automatically replaced on {} or `defaultValue`.
- Bug #6: Default value is ignored by "value" attribute of hidden input when Model is used.
- Fixed minor issue with `clientOptions`: `onChangeJSON`, `onChangeText`, `onClassName`, `onNodeName`, `onValidate`,
`onCreateMenu`, `schemaRefs`, `templates`, `autocomplete`, `onTextSelectionChange`, `onSelectionChange`, `onEvent`,
`onColorPicker`, `languages` and `modalAnchor`.
Now there is no need to use `JsExpression` for these options, extension will do it automatically.

2.2.0 September 8, 2019
-----------------------

- Enh: Updated npm-asset/jsoneditor version constraint to allow version 7.

2.1.0 June 21, 2019
-------------------

- Enh: Updated npm-asset/jsoneditor version constraint to allow version 6.

2.0.0 July 27, 2018
-------------------

- Chg: Removed inline style `height: 250px;` to simplify specification of custom height.
This change may affect your design. You can specify custom height like this:
    ```css
    div.jsoneditor {
        height: 250px;
    }
    ```

1.0.4 September 28, 2017
------------------------

- Bug #3: Widget doesn't work for tabular data input (multiple models in form).

1.0.3 March 29, 2017
--------------------

- Bug #2: Widget doesn't work if JSON contains `<script>` tag.

1.0.2 August 27, 2016
---------------------

- Fixed minor issue with `clientOptions`: `ace`, `ajv` and `schema`.
Now there is no need to use `JsExpression` for these options, extension will do it automatically.

1.0.1 August 25, 2016
---------------------

- Documentation. Minor optimization of generated JavaScript.

1.0.0 August 24, 2016
---------------------

- Initial release.
