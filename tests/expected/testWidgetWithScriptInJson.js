w6JsonEditor_a3048777 = new JSONEditor(document.getElementById('w6-json-editor'), {"mode":"tree","onChange":function() {jQuery('#w6').val(w6JsonEditor_a3048777.getText());}}, {"script":"\u003Cscript type=\u0022text\/javascript\u0022\u003Ealert(\u0022XSS\u0022);\u003C\/script\u003E"});
jQuery('#w6').parents('form').submit(function() {jQuery('#w6').val(w6JsonEditor_a3048777.getText());});