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
