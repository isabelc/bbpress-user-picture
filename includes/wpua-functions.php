<?php
/**
 * Public user functions.
 * 
 * @package WP User Avatar
 * @version 1.9.13
 */

/**
 * Returns true if user has wp_user_avatar
 * @since 1.8
 * @param int|string $id_or_email
 * @param bool $has_wpua
 * @param object $user
 * @param int $user_id
 * @uses object $wpua_functions
 * @return object has_wp_user_avatar()
 */
function has_wp_user_avatar($id_or_email="", $has_wpua="", $user="", $user_id="") {
  global $wpua_functions;
  return $wpua_functions->has_wp_user_avatar($id_or_email, $has_wpua, $user, $user_id);
}

/**
 * Find WPUA, show get_avatar if empty
 * @since 1.8
 * @param int|string $id_or_email
 * @param int|string $size
 * @param string $align
 * @param string $alt
 * @uses object $wpua_functions
 * @return object get_wp_user_avatar()
 */
function get_wp_user_avatar($id_or_email="", $size="", $align="", $alt="") {
  global $wpua_functions;
  return $wpua_functions->get_wp_user_avatar($id_or_email, $size, $align, $alt);
}

/**
 * Return just the image src
 * @since 1.8
 * @param int|string $id_or_email
 * @param int|string $size
 * @param string $align
 * @uses object $wpua_functions
 * @return object get_wp_user_avatar_src()
 */
function get_wp_user_avatar_src($id_or_email="", $size="", $align="") {
  global $wpua_functions;
  return $wpua_functions->get_wp_user_avatar_src($id_or_email, $size, $align);
}

/**
 * Before wrapper for profile
 * @since 1.6
 * @uses do_action()
 */
function wpua_before_avatar() {
  do_action('wpua_before_avatar');
}

/**
 * Before avatar container
 */
function wpua_do_before_avatar() {
  $wpua_profile_title = '<h3>'.__('Avatar','wp-user-avatar').'</h3>';
  /**
   * Filter profile title
   * @since 1.9.4
   * @param string $wpua_profile_title
   */
  $wpua_profile_title = apply_filters('wpua_profile_title', $wpua_profile_title);
?>
  <?php if(class_exists('bbPress') && bbp_is_edit()) : // Add to bbPress profile with same style ?>
    <fieldset id="bbp-user-pic" class="bbp-form">
      <legend><?php _e('Image','wp-user-avatar'); ?></legend>
  <?php else : ?>
    <div class="wpua-edit-container">
      <?php echo $wpua_profile_title; ?>
  <?php endif; ?>
  <?php
}
add_action('wpua_before_avatar', 'wpua_do_before_avatar');

/**
 * After avatar container
 */
function wpua_do_after_avatar() {
?>
  <?php if(class_exists('bbPress') && bbp_is_edit()) : // Add to bbPress profile with same style ?>
    </fieldset>
  <?php else : ?>
    </div>
  <?php endif; ?>
  <?php
}
add_action('wpua_after_avatar', 'wpua_do_after_avatar');

/**
 * Before wrapper for profile in admin section
 * @since 1.9.4
 * @uses do_action()
 */
function wpua_before_avatar_admin() {
  do_action('wpua_before_avatar_admin');
}
/**
 * Before avatar container in admin section
 * @since 1.9.4
 */
function wpua_do_before_avatar_admin() {
?>
  <h3><?php _e('Avatar') ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="wp_user_avatar"><?php _e('Image','wp-user-avatar'); ?></label></th>
      <td>
  <?php
}
add_action('wpua_before_avatar_admin', 'wpua_do_before_avatar_admin');

/**
 * After avatar container in admin section
 * @since 1.9.4
 */
function wpua_do_after_avatar_admin() {
?>
      </td>
    </tr>
  </table>
  <?php
}
add_action('wpua_after_avatar_admin', 'wpua_do_after_avatar_admin');
