modelmockDataJsonEditor_cb242086 = new JSONEditor(document.getElementById('modelmock-data-json-editor'), {"mode":"tree","onChange":function() {jQuery('#modelmock-data').val(modelmockDataJsonEditor_cb242086.getText());},"onModeChange":function(newMode, oldMode) {if (["tree"].indexOf(newMode) !== -1) {modelmockDataJsonEditor_cb242086.expandAll();}}});
modelmockDataJsonEditor_cb242086.set({});
jQuery('#modelmock-data').parents('form').submit(function() {jQuery('#modelmock-data').val(modelmockDataJsonEditor_cb242086.getText());});
modelmockDataJsonEditor_cb242086.expandAll();