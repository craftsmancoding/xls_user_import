INIT = {
	upload_form: function() {
		$('#fileselectbutton').click(function(e){
		  $('#file').trigger('click');
		 });
		    
		 $('#file').change(function(e){
		  var val = $(this).val();
		   
		  var file = val.split(/[\\/]/);
		   
		  $('#filename').val(file[file.length-1]);
		 });
		},

	drag_drop: function() {

		// create redips container
		var redips = {};


		// REDIPS.drag initialization
		redips.init = function () {
			REDIPS.drag.init();
		};



		// add onload event listener
		if (window.addEventListener) {
			window.addEventListener('load', redips.init, false);
		}
		else if (window.attachEvent) {
			window.attachEvent('onload', redips.init);
		}
	}
}

$(function() {
	INIT.upload_form();
	INIT.drag_drop();
});