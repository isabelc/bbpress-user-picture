<?php
/**
 * Defines all profile and upload settings.
 *
 * @package WP User Avatar
 */

class WP_User_Avatar {
	/**
	 * Constructor
	 */
	public function __construct() {
		global $pagenow, $wpua_admin;
		// Add WPUA to profile for users with permission
		if($this->wpua_is_author_or_above() || is_user_logged_in()) {
			// Profile functions and scripts
			add_action('show_user_profile', array('wp_user_avatar', 'wpua_action_show_user_profile'));
			add_action('personal_options_update', array($this, 'wpua_action_process_option_update'));
			add_action('edit_user_profile', array('wp_user_avatar', 'wpua_action_show_user_profile'));
			add_action('edit_user_profile_update', array($this, 'wpua_action_process_option_update'));
			add_action('user_new_form', array($this, 'wpua_action_show_user_profile'));
			add_action('user_register', array($this, 'wpua_action_process_option_update'));
			// Admin scripts
			$pages = array('profile.php', 'options-discussion.php', 'user-edit.php', 'user-new.php');
			if(in_array($pagenow, $pages) || $wpua_admin->wpua_is_menu_page()) {
				add_action('admin_enqueue_scripts', array($this, 'wpua_media_upload_scripts'));
			}
			// Front pages
			if(!is_admin()) {
				add_action('show_user_profile', array('wp_user_avatar', 'wpua_media_upload_scripts'));
				add_action('edit_user_profile', array('wp_user_avatar', 'wpua_media_upload_scripts'));
			}
			if(!$this->wpua_is_author_or_above()) {
				// Upload errors
				add_action('user_profile_update_errors', array($this, 'wpua_upload_errors'), 10, 3);
				// Prefilter upload size
				add_filter('wp_handle_upload_prefilter', array($this, 'wpua_handle_upload_prefilter'));
			}
		}
		add_filter('media_view_settings', array($this, 'wpua_media_view_settings'), 10, 1);
	}

	/**
	 * Avatars have no parent posts
	 * @param array $settings
	 */
	public function wpua_media_view_settings($settings) {
		global $post, $wpua_is_profile;
		// Get post ID so not to interfere with media uploads
		$post_id = is_object($post) ? $post->ID : 0;
		// Don't use post ID on front pages if there's a WPUA uploader
		$settings['post']['id'] = (!is_admin() && $wpua_is_profile == 1) ? 0 : $post_id;
		return $settings;
	}

	/**
	 * Media Uploader
	 */
	public static function wpua_media_upload_scripts($user="") {
		global $current_user, $mustache_admin, $pagenow, $post, $show_avatars, $wp_user_avatar, $wpua_admin, $wpua_functions, $wpua_is_profile, $wpua_upload_size_limit;
		// This is a profile page
		$wpua_is_profile = 1;
		$user = ($pagenow == 'user-edit.php' && isset($_GET['user_id'])) ? get_user_by('id', $_GET['user_id']) : $current_user;
		wp_enqueue_style('wp-user-avatar', WPUA_URL.'css/wp-user-avatar.css', "", WPUA_VERSION);
		wp_enqueue_script('jquery');
		if($wp_user_avatar->wpua_is_author_or_above()) {
			wp_enqueue_script('admin-bar');
			wp_enqueue_media(array('post' => $post));
			wp_enqueue_script('wp-user-avatar', WPUA_URL.'js/wp-user-avatar.js', array('jquery', 'media-editor'), WPUA_VERSION, true);
		} else {
			wp_enqueue_script('wp-user-avatar', WPUA_URL.'js/wp-user-avatar-user.js', array('jquery'), WPUA_VERSION, true);
		}
		// Admin scripts
		if($pagenow == 'options-discussion.php' || $wpua_admin->wpua_is_menu_page()) {
			// Size limit slider
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_style('wp-user-avatar-jqueryui', WPUA_URL.'css/jquery.ui.slider.css', "", null);
			// Default avatar
			wp_localize_script('wp-user-avatar', 'wpua_custom', array('avatar_thumb' => $mustache_admin));
			// Settings control
			wp_enqueue_script('wp-user-avatar-admin', WPUA_URL.'js/wp-user-avatar-admin.js', array('wp-user-avatar'), WPUA_VERSION, true);
			wp_localize_script('wp-user-avatar-admin', 'wpua_admin', array('upload_size_limit' => $wpua_upload_size_limit, 'max_upload_size' => wp_max_upload_size()));
		} else {
			// Original user avatar
			$avatar_medium_src = (bool) $show_avatars == 1 ? $wpua_functions->wpua_get_avatar_original($user->user_email, 'medium') : includes_url().'images/blank.gif';
			wp_localize_script('wp-user-avatar', 'wpua_custom', array('avatar_thumb' => $avatar_medium_src));
		}
	}

	/**
	 * Add to edit user profile
	 */
	public static function wpua_action_show_user_profile($user) {
		global $blog_id, $current_user, $show_avatars, $wpdb, $wp_user_avatar, $wpua_edit_avatar, $wpua_functions, $wpua_upload_size_limit;
		$has_wp_user_avatar = has_wp_user_avatar(@$user->ID);
		// Get WPUA attachment ID
		$wpua = get_user_meta(@$user->ID, $wpdb->get_blog_prefix($blog_id).'user_avatar', true);
		// Show remove button if WPUA is set
		$hide_remove = !$has_wp_user_avatar ? 'wpua-hide' : "";
		// Hide image tags if show avatars is off
		$hide_images = !$has_wp_user_avatar && (bool) $show_avatars == 0 ? 'wpua-no-avatars' : "";
		// If avatars are enabled, get original avatar image or show blank
		$avatar_medium_src = (bool) $show_avatars == 1 ? $wpua_functions->wpua_get_avatar_original(@$user->user_email, 'medium') : includes_url().'images/blank.gif';
		// Check if user has wp_user_avatar, if not show image from above
		$avatar_medium = $has_wp_user_avatar ? get_wp_user_avatar_src($user->ID, 'medium') : $avatar_medium_src;
		// Check if user has wp_user_avatar, if not show image from above
		$avatar_thumbnail = $has_wp_user_avatar ? get_wp_user_avatar_src($user->ID, 96) : $avatar_medium_src;
		$edit_attachment_link = esc_url(add_query_arg(array('post' => $wpua, 'action' => 'edit'), admin_url('post.php')));
	
		$is_admin = is_admin();

		if($is_admin) {
			?><h3>Avatar</h3><table class="form-table"><tr><th><label for="wp_user_avatar">Image</label></th>
			<td><?php
		} else {
				?><fieldset id="bbp-user-pic" class="bbp-form"><legend>Image</legend><?php
		}
		?>
		<input type="hidden" name="wp-user-avatar" id="<?php echo ($user=='add-new-user') ? 'wp-user-avatar' : 'wp-user-avatar-existing'?>" value="<?php echo $wpua; ?>" />
		<?php if($wp_user_avatar->wpua_is_author_or_above()) : // Button to launch Media Uploader ?>
			
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-add-button' : 'wpua-add-button-existing'?>"><button type="button" class="button" id="<?php echo ($user=='add-new-user') ? 'wpua-add' : 'wpua-add-existing'?>" name="<?php echo ($user=='add-new-user') ? 'wpua-add' : 'wpua-add-existing'?>" data-title="Choose Image: <?php echo ( ! empty( $user->display_name ) ? $user->display_name:''); ?>">Choose Image</button></p>

		<?php elseif(!$wp_user_avatar->wpua_is_author_or_above()) : // Upload button ?>
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-upload-button' : 'wpua-upload-button-existing'?>">
				<input name="wpua-file" id="<?php echo ($user=='add-new-user') ? 'wpua-file' : 'wpua-file-existing'?>" type="file" />
				<button type="submit" class="button" id="<?php echo ($user=='add-new-user') ? 'wpua-upload' : 'wpua-upload-existing'?>" name="submit" value="Upload">Upload</button>
			</p>
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-upload-messages' : 'wpua-upload-messages-existing'?>">
				<span id="<?php echo ($user=='add-new-user') ? 'wpua-max-upload' : 'wpua-max-upload-existing'?>" class="small"><?php printf('Maximum upload file size: %dMB.', ($wpua_upload_size_limit / 1048576)); ?></span>
				<span id="<?php echo ($user=='add-new-user') ? 'wpua-allowed-files' : 'wpua-allowed-files-existing'?>" class="small">Allowed Files: <code>jpg jpeg png gif</code></span>
			</p>
		<?php endif; ?>
		<div id="<?php echo ($user=='add-new-user') ? 'wpua-images' : 'wpua-images-existing'?>" class="<?php echo $hide_images; ?>">
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-thumbnail' : 'wpua-thumbnail-existing'?>">
				<img src="<?php echo $avatar_thumbnail; ?>" alt="" />
				<span class="description">Thumbnail</span>
			</p>
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-remove-button' : 'wpua-remove-button-existing'?>" class="<?php echo $hide_remove; ?>">
				<button type="button" class="secondary-btn" id="<?php echo ($user=='add-new-user') ? 'wpua-remove' : 'wpua-remove-existing'?>" name="wpua-remove">Remove Image</button>
				<?php if((bool) $wpua_edit_avatar == 1 && !$wp_user_avatar->wpua_is_author_or_above() && has_wp_user_avatar($current_user->ID) && $wp_user_avatar->wpua_author($wpua, $current_user->ID)) : // Edit button ?>
					<span id="<?php echo ($user=='add-new-user') ? 'wpua-edit-attachment' : 'wpua-edit-attachment-existing'?>"><a href="<?php echo $edit_attachment_link; ?>" class="edit-attachment" target="_blank">Edit Image</a></span>
				<?php endif; ?>
			</p>
			<p id="<?php echo ($user=='add-new-user') ? 'wpua-undo-button' : 'wpua-undo-button-existing'?>"><button type="button" class="button" id="<?php echo ($user=='add-new-user') ? 'wpua-undo' : 'wpua-undo-existing'?>" name="wpua-undo">Undo</button></p>
		</div><?php 
		if($is_admin) {
			?></td></tr></table><?php
		} else {
			?></fieldset><?php
		}
	
	}

	/**
	 * Add upload error messages
	 * @param array $errors
	 * @param bool $update
	 * @param object $user
	 */
	public static function wpua_upload_errors($errors, $update, $user) {
		global $wpua_upload_size_limit;
		if($update && !empty($_FILES['wpua-file'])) {
			$size = $_FILES['wpua-file']['size'];
			$type = $_FILES['wpua-file']['type'];
			$upload_dir = wp_upload_dir();
			if($_FILES['wpua-file']['error'] && empty($size) && empty($type)) {
				$errors->add('wpua_file_size', sprintf('Please try again. Image cannot be larger than %dMB.', ($wpua_upload_size_limit / 1048576)));
				return;
			}
			// Allow only JPG, GIF, PNG
			if(!empty($type) && !preg_match('/(jpe?g|gif|png)$/i', $type)) {
				$errors->add('wpua_file_type', 'This file is not an image. Please try another.');
			}
			// Upload size limit
			if(!empty($size) && $size > $wpua_upload_size_limit) {
				$errors->add('wpua_file_size', 'Memory exceeded. Please try another smaller file.');
			}
			// Check if directory is writeable
			if(!is_writeable($upload_dir['path'])) {
				$errors->add('wpua_file_directory', sprintf('Unable to create directory %s. Is its parent directory writable by the server?', $upload_dir['path']));
			}
		}
	}

	/**
	 * Set upload size limit
	 * @param object $file
	 * @return object $file
	 */
	public function wpua_handle_upload_prefilter($file) {
		global $wpua_upload_size_limit;
		$size = $file['size'];
		if(!empty($size) && $size > $wpua_upload_size_limit) {
			/**
			 * Error handling that only appears on front pages
			 */
			function wpua_file_size_error($errors, $update, $user) {
				$errors->add('wpua_file_size', 'Memory exceeded. Please try another smaller file.');
			}
			add_action('user_profile_update_errors', 'wpua_file_size_error', 10, 3);
			return;
		}
		return $file;
	}

	/**
	 * Update user meta
	 */
	public static function wpua_action_process_option_update($user_id) {
		global $blog_id, $post, $wpdb, $wp_user_avatar, $wpua_admin;
		$wpua_resize_crop = get_option('wp_user_avatar_resize_crop');
		$wpua_resize_w = get_option('wp_user_avatar_resize_w');
		$wpua_resize_h = get_option('wp_user_avatar_resize_h');
		$wpua_resize_upload = get_option('wp_user_avatar_resize_upload');
		// Check if user has publish_posts capability
		if($wp_user_avatar->wpua_is_author_or_above()) {
			$wpua_id = isset($_POST['wp-user-avatar']) ? strip_tags($_POST['wp-user-avatar']) : "";
			// Remove old attachment postmeta
			delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_id, true);
			// Create new attachment postmeta
			add_post_meta($wpua_id, '_wp_attachment_wp_user_avatar', $user_id);
			// Update usermeta
			update_user_meta($user_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', $wpua_id);
		} else {
			// Remove attachment info if avatar is blank
			if(isset($_POST['wp-user-avatar']) && empty($_POST['wp-user-avatar'])) {
				// Delete other uploads by user
				$q = array(
					'author' => $user_id,
					'post_type' => 'attachment',
					'post_status' => 'inherit',
					'posts_per_page' => '-1',
					'meta_query' => array(
						array(
							'key' => '_wp_attachment_wp_user_avatar',
							'value' => "",
							'compare' => '!='
						)
					)
				);
				$avatars_wp_query = new WP_Query($q);
				while($avatars_wp_query->have_posts()) : $avatars_wp_query->the_post();
					wp_delete_attachment($post->ID);
				endwhile;
				wp_reset_query();
				// Remove attachment postmeta
				delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_id, true);
				// Remove usermeta
				update_user_meta($user_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', "");
			}
			// Create attachment from upload
			if(isset($_POST['submit']) && $_POST['submit'] && !empty($_FILES['wpua-file']) && empty($_FILES['wpua-file']['error'])) {
				$name = $_FILES['wpua-file']['name'];
				$file = wp_handle_upload($_FILES['wpua-file'], array('test_form' => false));
				$type = $_FILES['wpua-file']['type'] ?? '';
				$upload_dir = wp_upload_dir();
				if(is_writeable($upload_dir['path'])) {
					if(!empty($type) && preg_match('/(jpe?g|gif|png)$/i', $type)) {
						// Resize uploaded image
						if((bool) $wpua_resize_upload == 1) {
							// Original image
							$uploaded_image = wp_get_image_editor($file['file']);
							// Check for errors
							if(!is_wp_error($uploaded_image)) {
								// Resize image
								$uploaded_image->resize($wpua_resize_w, $wpua_resize_h, $wpua_resize_crop);
								// Save image
								$resized_image = $uploaded_image->save($file['file']);
							}
						}
						// Break out file info
						$name_parts = pathinfo($name);
						$name = trim(substr($name, 0, -(1 + strlen($name_parts['extension']))));
						$url = $file['url'];
						$file = $file['file'];
						$title = $name;
						// Use image exif/iptc data for title if possible
						if($image_meta = @wp_read_image_metadata($file)) {
							if(trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
								$title = $image_meta['title'];
							}
						}
						// Construct the attachment array
						$attachment = array(
							'guid'           => $url,
							'post_mime_type' => $type,
							'post_title'     => $title,
							'post_content'   => ""
						);
						// This should never be set as it would then overwrite an existing attachment
						if(isset($attachment['ID'])) {
							unset($attachment['ID']);
						}
						// Save the attachment metadata
						$attachment_id = wp_insert_attachment($attachment, $file);
						if(!is_wp_error($attachment_id)) {
							// Delete other uploads by user
							$q = array(
								'author' => $user_id,
								'post_type' => 'attachment',
								'post_status' => 'inherit',
								'posts_per_page' => '-1',
								'meta_query' => array(
									array(
										'key' => '_wp_attachment_wp_user_avatar',
										'value' => "",
										'compare' => '!='
									)
								)
							);
							$avatars_wp_query = new WP_Query($q);
							while($avatars_wp_query->have_posts()) : $avatars_wp_query->the_post();
								wp_delete_attachment($post->ID);
							endwhile;
							wp_reset_query();
							wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $file));
							// Remove old attachment postmeta
							delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_id, true);
							// Create new attachment postmeta
							update_post_meta($attachment_id, '_wp_attachment_wp_user_avatar', $user_id);
							// Update usermeta
							update_user_meta($user_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', $attachment_id);
						}
					}
				}
			}
		}
	}

	/**
	 * Check attachment is owned by user
	 * @param int $attachment_id
	 * @param int $user_id
	 * @param bool $wpua_author
	 * @return bool 
	 */
	private function wpua_author($attachment_id, $user_id, $wpua_author=0) {
		$attachment = get_post($attachment_id);
		if(!empty($attachment) && $attachment->post_author == $user_id) {
			$wpua_author = true;
		}
		return (bool) $wpua_author;
	}

	/**
	 * Check if current user has at least Author privileges
	 * @return bool
	 */
	public function wpua_is_author_or_above() {
		$is_author_or_above = (current_user_can('edit_published_posts') && current_user_can('upload_files') && current_user_can('publish_posts') && current_user_can('delete_published_posts')) ?: false;
		return $is_author_or_above;
	}
}

/**
 * Initialize WP_User_Avatar
 */
function wpua_init() {
	global $wp_user_avatar;
	$wp_user_avatar = new WP_User_Avatar();
}
add_action('init', 'wpua_init');
