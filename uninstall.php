<?php
/**
 * Remove user metadata and options on plugin delete.
 */
if(!defined('WP_UNINSTALL_PLUGIN')) exit;
global $blog_id, $wpdb;
$users = get_users();
// Remove settings for all sites in multisite
if(is_multisite()) {
	$blogs = wp_get_sites();
	foreach($users as $user) {
		foreach($blogs as $blog) {
			delete_user_meta($user->ID, $wpdb->get_blog_prefix($blog->blog_id).'user_avatar');
		}
	}
	foreach($blogs as $blog) {
		switch_to_blog($blog->blog_id);
		bbpup_uninstall();
		restore_current_blog();
	}
} else {
	foreach($users as $user) {
		delete_user_meta($user->ID, $wpdb->get_blog_prefix($blog_id).'user_avatar');
	}
	bbpup_uninstall();
}
function bbpup_uninstall() {
	global $wp_roles;
	delete_option('avatar_default_wp_user_avatar');
	delete_option('wp_user_avatar_edit_avatar');
	delete_option('wp_user_avatar_load_scripts');
	delete_option('wp_user_avatar_resize_crop');
	delete_option('wp_user_avatar_resize_h');
	delete_option('wp_user_avatar_resize_upload');
	delete_option('wp_user_avatar_resize_w');
	delete_option('wp_user_avatar_upload_size_limit');
	delete_post_meta_by_key('_wp_attachment_wp_user_avatar');
	// Reset all default avatars to Mystery Man
	update_option('avatar_default', 'mystery');	
	$wp_roles->remove_cap('subscriber', 'edit_posts');
}
