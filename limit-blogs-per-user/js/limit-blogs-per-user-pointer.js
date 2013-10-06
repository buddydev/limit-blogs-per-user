jQuery(document).ready(function($) {
	pointer = tiw_pointer.pointers['tiw-create-a-blog'];

	if (!pointer) return;
	
	options = $.extend( pointer.options, {
		close: function() {
			$.post( ajaxurl, {
				pointer: pointer.pointer_id,
				action: 'dimiss-wp-pointer'
			})
		}
	});

	$(pointer.target).pointer( options ).pointer('open');
});