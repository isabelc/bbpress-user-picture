jQuery(function($) {

	// Disable Upload button until they select a file

	$("#wpua-upload-existing").prop('disabled', true);
	$("#wpua-file-existing").change(function(){
		$("#wpua-upload-existing").prop('disabled', false); 
	});
	
	// @test still need all css?

	// Store Existing User Avatar ID
	var wpuaEID = $('#wp-user-avatar-existing').val();

	// Store existing User Avatar src
	var bbpupEsrc = document.getElementById('preview-thumb').src;

	// Remove WP Existing User Avatar
	$('body').on('click', '#wpua-remove-existing', function(e) {
		e.preventDefault();
	
		$('#wpua-remove-button-existing').hide();

		$('#wp-user-avatar-existing').val('');

		$('#wpua-undo-button-existing').show();

		$('#preview-thumb').attr('src', wpua_custom.default);

	});

	// Undo WP Existing User Avatar
	$('body').on('click', '#wpua-undo-existing', function(e) {
		e.preventDefault();
		
		$('#wpua-undo-button-existing').hide();

		$('#wpua-remove-button-existing').show();

		$('#wp-user-avatar-existing').val(wpuaEID);

		$('#preview-thumb').attr('src', bbpupEsrc);

	});
});
