jQuery(window).load(function(){
    var textarea =  document.querySelectorAll('#carbon_fields_container_simple_bot textarea');
    if (textarea.length === 0) {
	var textarea =  document.querySelectorAll('.container-SimpleBot textarea');
    }		
    if (textarea.length > 0) {
    for (var i = 0; i < textarea.length; i++) {
        textarea[i].addEventListener('click', function(event) {
            var editor = CodeMirror.fromTextArea(this, {
                lineNumbers: true,
                lineWrapping: true
            });
        });
    }
    }	
});



