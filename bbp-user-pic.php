<?php
/*
Plugin Name: bbPress User Picture
Description: Let bbPress forum users upload their own profile image.
Version: 1.0.alpha-6
Requires PHP: 7.2
Author: Isabel Castillo
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

/**
 * Let's get started!
 */
class WP_User_Avatar_Setup {
  /**
   * Constructor
   * @since 1.9.2
   */
  public function __construct() {
    $this->_define_constants();
    $this->_load_wp_includes();
    $this->_load_wpua();
  }

  /**
   * Define paths
   * @since 1.9.2
   */
  private function _define_constants() {
    define('WPUA_VERSION', '2.2.4');
    define('WPUA_FOLDER', basename(dirname(__FILE__)));
    define('WPUA_DIR', plugin_dir_path(__FILE__));
    define('WPUA_INC', WPUA_DIR.'includes'.'/');
    define('WPUA_URL', plugin_dir_url(WPUA_FOLDER).WPUA_FOLDER.'/');
    define('WPUA_INC_URL', WPUA_URL.'includes'.'/');
  }

  /**
   * WordPress includes used in plugin
   * @since 1.9.2
   * @uses is_admin()
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
    require_once(WPUA_INC.'wpua-globals.php');
    require_once(WPUA_INC.'wpua-functions.php');
    require_once(WPUA_INC.'class-wp-user-avatar-admin.php');
    require_once(WPUA_INC.'class-wp-user-avatar.php');
    require_once(WPUA_INC.'class-wp-user-avatar-functions.php');
    require_once(WPUA_INC.'class-wp-user-avatar-subscriber.php');
  }
}

/**
 * Initialize
 */
new WP_User_Avatar_Setup();
