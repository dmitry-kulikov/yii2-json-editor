Change Log
==========

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

- Fixed minor issue with `$clientOptions`: `ace`, `ajv` and `schema`.
Now there is no need to use `JsExpression` for these options, extension will do it automatically.

1.0.1 August 25, 2016
---------------------

- Documentation. Minor optimization of generated JavaScript.

1.0.0 August 24, 2016
---------------------

- Initial release.
