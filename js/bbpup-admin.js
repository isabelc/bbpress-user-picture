jQuery(function($) {
  // Show resize info only if resize uploads is checked
  $('#wp_user_avatar_resize_upload').change(function() {
     $('#wpua-resize-sizes').slideToggle($('#wp_user_avatar_resize_upload').is(':checked'));
  });
  // Add size slider
  $('#wpua-slider').slider({
    value: parseInt(wpua_admin.upload_size_limit),
    min: 0,
    max: parseInt(wpua_admin.max_upload_size),
    step: 1,
    slide: function(event, ui) {
      $('#wp_user_avatar_upload_size_limit').val(ui.value);
      $('#wpua-readable-size').html(Math.floor(ui.value / 1024) + 'KB');
      $('#wpua-readable-size-error').hide();
      $('#wpua-readable-size').removeClass('wpua-error');
    }
  });
  // Update readable size on keyup
  $('#wp_user_avatar_upload_size_limit').keyup(function() {
    var wpuaUploadSizeLimit = $(this).val();
    wpuaUploadSizeLimit = wpuaUploadSizeLimit.replace(/\D/g, "");
    $(this).val(wpuaUploadSizeLimit);
    $('#wpua-readable-size').html(Math.floor(wpuaUploadSizeLimit / 1024) + 'KB');
    $('#wpua-readable-size-error').toggle(wpuaUploadSizeLimit > parseInt(wpua_admin.max_upload_size));
    $('#wpua-readable-size').toggleClass('wpua-error', wpuaUploadSizeLimit > parseInt(wpua_admin.max_upload_size));
  });
  $('#wp_user_avatar_upload_size_limit').val($('#wpua-slider').slider('value'));
});
