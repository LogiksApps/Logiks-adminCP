<?php
loadModule("editor");
if(isset($_REQUEST['editor'])) {
	if(in_array($_REQUEST['editor'],listEditors())) {
		loadEditor($_REQUEST['editor']);
	} else {
		loadEditor("nicedit");
	}	
} else {
	loadEditor("ckeditor");
}
?>
<textarea id=one style='font:14px bold Arial;'>Hello World</textarea>
<script>
$(function() {
	$("#one").css("height","80%");
	$("#one").css("width","99%");
	if(typeof window.loadEditor == "function") loadEditor("one");
});
</script>
