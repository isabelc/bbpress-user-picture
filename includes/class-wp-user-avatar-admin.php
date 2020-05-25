<?php
/**
 * Defines all of administrative, activation, and deactivation settings.
 *
 * @package WP User Avatar
 */

class WP_User_Avatar_Admin {
	public function __construct() {
		// Admin menu settings
		add_action('admin_menu', array($this, 'wpua_admin'));
		add_action('admin_init', array($this, 'wpua_register_settings'));
		// Default avatar
		add_filter('default_avatar_select', array($this, 'wpua_add_default_avatar'), 10);
		add_filter('whitelist_options', array($this, 'wpua_whitelist_options'), 10);
		add_filter('plugin_action_links', array($this, 'wpua_action_links'), 10, 2);
		// Media states
		add_filter('display_media_states', array($this, 'wpua_add_media_state'), 10, 1);
	}
	/**
	 * On deactivation
	 * @todo decide if this is still needed and if so, move it to mail file.
	 */
	public function wpua_deactivate() {
		global $blog_id, $wpdb;
		$wp_user_roles = $wpdb->get_blog_prefix($blog_id).'user_roles';
		// Get user roles and capabilities
		$user_roles = get_option($wp_user_roles);
		// Remove subscribers edit_posts capability
		unset($user_roles['subscriber']['capabilities']['edit_posts']);
		update_option($wp_user_roles, $user_roles);
		// Reset all default avatars to Mystery Man
		update_option('avatar_default', 'mystery');
	
	}

	/**
	 * Add options page and settings
	 */
	public function wpua_admin() {
		add_menu_page('bbPress User Picture', 'Avatars', 'manage_options', 'wp-user-avatar', array($this, 'wpua_options_page'), BBPUP_URL.'images/wpua-icon.png');
		add_submenu_page('wp-user-avatar', 'Settings', 'Settings', 'manage_options', 'wp-user-avatar', array($this, 'wpua_options_page'));
		$hook = add_submenu_page('wp-user-avatar', 'Library', 'Library', 'manage_options', 'wp-user-avatar-library', array($this, 'wpua_media_page'));
		add_action("load-$hook", array($this, 'wpua_media_screen_option'));
		add_filter('set-screen-option', array($this, 'wpua_set_media_screen_option'), 10, 3);
	}

	/**
	 * Checks if current page is settings page
	 * @return bool
	 */
	public function wpua_is_menu_page() {
		global $pagenow;
		$is_menu_page = ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'wp-user-avatar') ? true : false;
		return (bool) $is_menu_page;
	}

	/**
	 * Media page
	 */
	public function wpua_media_page() {
		require_once BBPUP_INC.'wpua-media-page.php';
	}

	/**
	 * Avatars per page
	 */
	public function wpua_media_screen_option() {
		$option = 'per_page';
		$args = array(
			'label' => 'Avatars',
			'default' => 10,
			'option' => 'upload_per_page'
		);
		add_screen_option($option, $args);
	}

	/**
	 * Save per page setting
	 * @return int $status
	 */
	public function wpua_set_media_screen_option($status, $option, $value) {
		$status = ($option == 'upload_per_page') ? $value : $status;
		return $status;
	}

	/**
	 * Options page
	 */
	public function wpua_options_page() {
		require_once BBPUP_INC.'wpua-options-page.php';
	}

	/**
	 * Whitelist settings
	 */
	public function wpua_register_settings() {
		$settings = array();
		$settings[] = register_setting('wpua-settings-group', 'avatar_default');
		$settings[] = register_setting('wpua-settings-group', 'avatar_default_wp_user_avatar');
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_edit_avatar', array('sanitize_callback' => array($this, 'subscriber_capability_edit_posts')));// @test now
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_resize_crop', array('sanitize_callback' => 'intval'));
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_resize_h', array('sanitize_callback' => 'intval'));
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_resize_upload', array('sanitize_callback' => 'intval'));
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_resize_w', array('sanitize_callback' => 'intval'));
		$settings[] = register_setting('wpua-settings-group', 'wp_user_avatar_upload_size_limit', array('sanitize_callback' => 'intval'));
		return $settings;
	}
	/**
	 * Add default avatar
	 * @return string
	 */
	public function wpua_add_default_avatar() {
		global $avatar_default, $mustache_admin, $wpua_avatar_default, $wpua_functions;
		// Remove get_avatar filter
		remove_filter('get_avatar', array($wpua_functions, 'wpua_get_avatar_filter'));
		// Show remove link if custom Default Avatar is set
		if(!empty($wpua_avatar_default) && wp_attachment_is_image($wpua_avatar_default)) {
			$avatar_thumb_src = wp_get_attachment_image_src($wpua_avatar_default, array(32,32));
			$avatar_thumb = $avatar_thumb_src[0];
			$hide_remove = "";
		} else {
			$avatar_thumb = $mustache_admin;
			$hide_remove = ' class="wpua-hide"';
		}
		// Default Avatar is wp_user_avatar, check the radio button next to it
		$selected_avatar = ($avatar_default == 'wp_user_avatar') ? ' checked="checked" ' : "";
		// Wrap WPUA in div
		$avatar_thumb_img = '<div id="wpua-preview"><img src="'.$avatar_thumb.'" width="32" /></div>';
		// Add WPUA to list
		$wpua_list = "\n\t<label><input type='radio' name='avatar_default' id='wp_user_avatar_radio' value='wp_user_avatar'$selected_avatar /> ";
		$wpua_list .= preg_replace("/src='(.+?)'/", "src='\$1'", $avatar_thumb_img);
		$wpua_list .= ' Default Avatar</label>';
		$wpua_list .= '<p id="wpua-edit"><button type="button" class="button" id="wpua-add" name="wpua-add" data-avatar_default="true" data-title="Choose Image: Default Avatar">Choose Image</button>';
		$wpua_list .= '<span id="wpua-remove-button"'.$hide_remove.'><a href="#" id="wpua-remove">Remove</a></span><span id="wpua-undo-button"><a href="#" id="wpua-undo">Undo</a></span></p>';
		$wpua_list .= '<input type="hidden" id="wp-user-avatar" name="avatar_default_wp_user_avatar" value="'.$wpua_avatar_default.'">';
		$wpua_list .= '<div id="wpua-modal"></div>';
		return $wpua_list;
	}
	/**
	 * Add default avatar_default to whitelist
	 */
	public function wpua_whitelist_options($options) {
		$options['discussion'][] = 'avatar_default_wp_user_avatar';
		return $options;
	}
	public function subscriber_capability_edit_posts($input) {
		// if setting changed, change subscriber capability
		$old_val = (int) get_option('wp_user_avatar_edit_avatar');
		$new_val = (int) $input;
		if($old_val !== $new_val) {
		    global $blog_id, $wpdb, $wpua_edit_avatar;
	    	$wp_user_roles = $wpdb->get_blog_prefix($blog_id).'user_roles';
	    	$user_roles = get_option($wp_user_roles);
			if(1 === $new_val) {
				$user_roles['subscriber']['capabilities']['edit_posts'] = true;
			} else {
				if(isset($user_roles['subscriber']['capabilities']['edit_posts'])){
					unset($user_roles['subscriber']['capabilities']['edit_posts']);
				}
			}
			update_option($wp_user_roles, $user_roles);
		}
	    return $new_val;
	}
	/**
	 * Add actions links on plugin page
	 * @param array $links
	 * @param string $file
	 * @return array $links
	 */
	public function wpua_action_links($links, $file) { 
		if(basename(dirname($file)) == 'bbp-user-pic') {
			$links[] = '<a href="'.esc_url(add_query_arg(array('page' => 'wp-user-avatar'), admin_url('admin.php'))).'">Settings</a>';
		}
		return $links;
	}
	/**
	 * Get list table
	 * @param string $class
	 * @param array $args
	 * @return object
	 */
	public function _wpua_get_list_table($class, $args = array()) {
		require_once BBPUP_INC.'class-wp-user-avatar-list-table.php';
		$args['screen'] = 'wp-user-avatar';
		return new $class($args);
	}

	/**
	 * Add media states
	 * @return array
	 */
	public function wpua_add_media_state($states) {
		global $post, $wpua_avatar_default;
		$is_wpua = get_post_custom_values('_wp_attachment_wp_user_avatar', $post->ID);
		if(!empty($is_wpua)) {
			$states[] = 'Avatar';
		}
		if(!empty($wpua_avatar_default) && ($wpua_avatar_default == $post->ID)) {
			$states[] = 'Default Avatar';
		}
		return $states;
	}
	
}

/**
 * Initialize
 */
function wpua_admin_init() {
	global $wpua_admin;
	$wpua_admin = new WP_User_Avatar_Admin();
}
add_action('init', 'wpua_admin_init');
