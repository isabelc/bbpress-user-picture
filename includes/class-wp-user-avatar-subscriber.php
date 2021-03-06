<?php
/**
 * Settings only for subscribers and contributors.
 *
 * @package WP User Avatar
 */

class WP_User_Avatar_Subscriber {
	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_user_avatar;
		add_action('user_edit_form_tag', array($this, 'wpua_add_edit_form_multipart_encoding'));
		add_action('wp_dashboard_setup', array($this, 'all_remove_dashboard_widgets'));
		add_action('wp_dashboard_setup', array($this, 'subscriber_remove_dashboard_widgets'));
		// Only Subscribers lack delete_posts capability
		if(!current_user_can('delete_posts') && current_user_can('edit_posts') && !$wp_user_avatar->wpua_is_author_or_above()) {
			add_action('admin_menu', array($this, 'wpua_subscriber_remove_menu_pages'));
			add_action('wp_before_admin_bar_render', array($this, 'wpua_subscriber_remove_menu_bar_items'));
			add_action('wp_dashboard_setup', array($this, 'subscriber_edit_remove_dashboard_widgets'));
			add_action('admin_init', array($this, 'wpua_subscriber_offlimits'));
		}
	}
	/**
	 * Allow multipart data in form
	 */
	public function wpua_add_edit_form_multipart_encoding() {
		echo ' enctype="multipart/form-data"';
	}
	/**
	 * Remove menu items
	 */
	public function wpua_subscriber_remove_menu_pages() {
		remove_menu_page('edit.php');
		remove_menu_page('edit-comments.php');
		remove_menu_page('tools.php');
	}
	/**
	 * Remove menu bar items
	 */
	public function wpua_subscriber_remove_menu_bar_items() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
		$wp_admin_bar->remove_menu('new-content');
	}
	/**
	 * Remove dashboard items for all
	 */
	public function all_remove_dashboard_widgets() {
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
	}
	/**
	 * Remove dashboard items only for Subscriber
	 */
	public function subscriber_remove_dashboard_widgets() {
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
		remove_meta_box('tinypng_dashboard_widget', 'dashboard', 'normal');
	}
	/**
	 * Remove dashboard items only for Subscriber that can edit_posts
	 */
	public function subscriber_edit_remove_dashboard_widgets() {
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
	}
	/**
	 * Restrict access to pages
	 */
	public function wpua_subscriber_offlimits() {
		global $pagenow;
		$can_edit_avatar = get_option('wp_user_avatar_edit_avatar');
		$offlimits = array('edit.php', 'edit-comments.php', 'post-new.php', 'tools.php');
		if((bool) $can_edit_avatar != 1) {
			array_push($offlimits, 'post.php');
		}
		if(in_array($pagenow, $offlimits)) {
			do_action('admin_page_access_denied');
			wp_die('You do not have sufficient permissions to access this page.');
		}
	}
}
/**
 * Initialize
 */
function wpua_subscriber_init() {
	global $wpua_subscriber;
	$wpua_subscriber = new WP_User_Avatar_Subscriber();
}
add_action('init', 'wpua_subscriber_init');
