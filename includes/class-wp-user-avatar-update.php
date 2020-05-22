<?php
/**
 * Updates for legacy settings.
 *
 * @package WP User Avatar
 * @version 1.9.13
 */

class WP_User_Avatar_Update {
  /**
   * Constructor
   * @since 1.8
   * @uses bool $wpua_media_updated
   * @uses add_action()
   */
  public function __construct() {
    global $wpua_media_updated;
    if(empty($wpua_media_updated)) {
      add_action('admin_init', array($this, 'wpua_media_state'));// @test need?
    }
  }
  /**
   * Add media state to existing avatars
   * @since 1.4
   * @uses int $blog_id
   * @uses object $wpdb
   * @uses add_post_meta()
   * @uses get_blog_prefix()
   * @uses get_results()
   * @uses update_option()
   */
  public function wpua_media_state() {
    global $blog_id, $wpdb;
    // Find all users with WPUA
    $wpua_metakey = $wpdb->get_blog_prefix($blog_id).'user_avatar';
    $wpuas = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value != %d AND meta_value != %d", $wpua_metakey, 0, ""));
    foreach($wpuas as $usermeta) {
      add_post_meta($usermeta->meta_value, '_wp_attachment_wp_user_avatar', $usermeta->user_id);
    }
    update_option('wp_user_avatar_media_updated', '1');
  }
}

/**
 * Initialize
 * @since 1.9.2
 */
function wpua_update_init() {
  global $wpua_update;
  $wpua_update = new WP_User_Avatar_Update();
}
add_action('init', 'wpua_update_init');
