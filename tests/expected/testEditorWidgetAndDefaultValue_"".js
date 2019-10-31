dataJsonEditor_729ee6af = new JSONEditor(document.getElementById('data-json-editor'), {"mode":"tree","onChange":function() {jQuery('#data').val(dataJsonEditor_729ee6af.getText());}});
dataJsonEditor_729ee6af.set("");
jQuery('#data').parents('form').submit(function() {jQuery('#data').val(dataJsonEditor_729ee6af.getText());});