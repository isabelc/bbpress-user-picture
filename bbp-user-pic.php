<?php
/*
Plugin Name: bbPress User Picture
Description: Let bbPress forum users upload their own profile image.
Version: 1.0.alpha-7
Requires PHP: 7.2
Author: Isabel Castillo
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;
class WP_User_Avatar_Setup {
  public function __construct() {
    $this->_define_constants();
    $this->_load_wp_includes();
    $this->_load_wpua();
  }

  /**
   * Define paths
   */
  private function _define_constants() {
    define('BBPUP_INC', plugin_dir_path(__FILE__).'includes'.'/');
    define('BBPUP_URL', plugin_dir_url(__FILE__).'/');
  }

  /**
   * WordPress includes used in plugin
   */
  private function _load_wp_includes() {
    if(!is_admin()) {
      // wp_handle_upload
      require_once(ABSPATH.'wp-admin/includes/file.php');
      // wp_generate_attachment_metadata
      require_once(ABSPATH.'wp-admin/includes/image.php');
      // image_add_caption
      require_once(ABSPATH.'wp-admin/includes/media.php');
      // submit_button
      require_once(ABSPATH.'wp-admin/includes/template.php');
    }
    // add_screen_option
    require_once(ABSPATH.'wp-admin/includes/screen.php');
  }

  /**
   * Load WP User Avatar
   */
  private function _load_wpua() {
    require_once(BBPUP_INC.'wpua-globals.php');
    require_once(BBPUP_INC.'wpua-functions.php');
    require_once(BBPUP_INC.'class-wp-user-avatar-admin.php');
    require_once(BBPUP_INC.'class-wp-user-avatar.php');
    require_once(BBPUP_INC.'class-wp-user-avatar-functions.php');
    require_once(BBPUP_INC.'class-wp-user-avatar-subscriber.php');
  }
}

/**
 * Initialize
 */
new WP_User_Avatar_Setup();
// default settings
function bbp_user_pic_activate() {
    add_option('avatar_default_wp_user_avatar', "");
    add_option('wp_user_avatar_edit_avatar', '0');
    add_option('wp_user_avatar_resize_crop', '0');
    add_option('wp_user_avatar_resize_h', '100');
    add_option('wp_user_avatar_resize_upload', '0');
    add_option('wp_user_avatar_resize_w', '100');
    add_option('wp_user_avatar_upload_size_limit', '0');
    update_option('avatar_default', 'wp_user_avatar');
}
register_activation_hook(__FILE__, 'bbp_user_pic_activate');
