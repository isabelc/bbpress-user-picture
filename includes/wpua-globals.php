<?php
/**
 * Global variables used in plugin.
 */
// Define global variables
global $avatar_default,
       $wpua_avatar_default,
       $mustache_original,
       $mustache_medium,
       $mustache_thumbnail,
       $mustache_avatar,
       $mustache_admin,
       $wpua_upload_size_limit;

// Default avatar name
$avatar_default = get_option('avatar_default');
// Attachment ID of default avatar
$wpua_avatar_default = get_option('avatar_default_wp_user_avatar');
// Default avatar 100x100
$mustache_original = BBPUP_URL.'images/bbpup.png';
// Default avatar 60x60
$mustache_medium = BBPUP_URL.'images/bbpup-60x60.png';
// Default avatar 50x50
$mustache_thumbnail = BBPUP_URL.'images/bbpup-50x50.png';
// Default avatar 40x40
$mustache_avatar = BBPUP_URL.'images/bbpup-40x40.png';
// Default avatar 30x30
$mustache_admin = BBPUP_URL.'images/bbpup-30x30.png';
// User upload size limit in bytes
$wpua_upload_size_limit = get_option('wp_user_avatar_upload_size_limit');
if($wpua_upload_size_limit == 0 || $wpua_upload_size_limit > wp_max_upload_size()) {
  $wpua_upload_size_limit = wp_max_upload_size();
}
