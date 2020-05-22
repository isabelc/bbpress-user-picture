<?php
/**
 * Core user functions.
 * 
 * @package WP User Avatar
 * @version 2.2.4
 */

class WP_User_Avatar_Functions {
  /**
   * Constructor
   * @since 1.8
   * @uses add_filter()
   * @uses register_activation_hook()
   * @uses register_deactivation_hook()
   */
  public function __construct() {
    add_filter('get_avatar', array($this, 'wpua_get_avatar_filter'), 10, 5);

    add_filter( 'get_avatar_url', array($this,'wpua_get_avatar_url'), 10, 3 );
  }
  function wpua_get_avatar_url($url, $id_or_email, $args){
    $user_id=null;
    if(is_object($id_or_email)){
       if(!empty($id_or_email->comment_author_email)) {
          $user_id = $id_or_email->user_id;
        }

    }else{
      if ( is_email( $id_or_email ) ) {
        $user = get_user_by( 'email', $id_or_email );
        if($user){
          $user_id = $user->ID;
        }
      } else {
        $user_id = $id_or_email;
      }
    }

    // First checking custom avatar.
    if( has_wp_user_avatar( $user_id ) ) {

      $url = $this->get_wp_user_avatar_src( $user_id );

    } else {

      $url = $this->wpua_get_default_avatar_url($url, $id_or_email, $args);
    
    }
    return $url;
  }


  function wpua_get_default_avatar_url($url, $id_or_email, $args){

        global $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $wpua_avatar_default, $wpua_functions;
        
        $default_image_details = array();

        $size = !empty($args['size'])?$args['size']:100;
        
        // Show custom Default Avatar
        if(!empty($wpua_avatar_default) && $wpua_functions->wpua_attachment_is_image($wpua_avatar_default)) {
          // Get image
          $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, array($size,$size));
          // Image src
          $url = $wpua_avatar_default_image[0];
          // Add dimensions if numeric size        
        } else {
          // Get mustache image based on numeric size comparison
          if($size >= 100) {
            $url = $mustache_original;
          } elseif($size < 100 && $size >= 60) {
            $url = $mustache_medium;
          } elseif($size < 60 && $size >= 50) {
            $url = $mustache_thumbnail;
          } elseif($size < 50 && $size >= 40) {
            $url = $mustache_avatar;
          } elseif($size < 40) {
            $url = $mustache_admin;
          }
          // Add dimensions if numeric size
        }

        return $url;
  }
  /**
   * Check if local image
   * @return bool
   */
  public function wpua_attachment_is_image($attachment_id) {
    return (bool) wp_attachment_is_image($attachment_id);
  }

  /**
   * Get local image tag
   * @return string
   */
  public function wpua_get_attachment_image($attachment_id, $size='thumbnail', $icon=0, $attr='') {
    return wp_get_attachment_image($attachment_id, $size, $icon, $attr);
  }

  /**
   * @return array
   */
  public function wpua_get_attachment_image_src($attachment_id, $size='thumbnail', $icon=0) {
    return wp_get_attachment_image_src($attachment_id, $size, $icon);
  }

  /**
   * Returns true if user has wp_user_avatar
   * @since 1.1
   * @param int|string $id_or_email
   * @param bool $has_wpua
   * @param object $user
   * @param int $user_id
   * @uses int $blog_id
   * @uses object $wpdb
   * @uses int $wpua_avatar_default
   * @uses object $wpua_functions
   * @uses get_user_by()
   * @uses get_user_meta()
   * @uses get_blog_prefix()
   * @uses wpua_attachment_is_image()
   * @return bool
   */
  public function has_wp_user_avatar($id_or_email="", $has_wpua=0, $user="", $user_id="") {
    global $blog_id, $wpdb, $wpua_avatar_default, $wpua_functions, $avatar_default;
    if(!is_object($id_or_email) && !empty($id_or_email)) {
      // Find user by ID or e-mail address

      $user = is_numeric($id_or_email) ? get_user_by('id', $id_or_email) : get_user_by('email', $id_or_email);
      // Get registered user ID
       $user_id = !empty($user) ? $user->ID : "";
    }
    $wpua = get_user_meta($user_id, $wpdb->get_blog_prefix($blog_id).'user_avatar', true);
    // Check if avatar is same as default avatar or on excluded list
    $has_wpua = !empty($wpua) && ($avatar_default!='wp_user_avatar' or $wpua != $wpua_avatar_default) && $wpua_functions->wpua_attachment_is_image($wpua) ? true : false;
    return (bool) $has_wpua;
  }
  /**
  Retrive default image url set by admin. 
  */
  public function wpua_default_image($size)
  {
        global $mustache_admin, $mustache_avatar, $mustache_medium, $mustache_original, $mustache_thumbnail, $wpua_avatar_default, $wpua_functions;
        
        $default_image_details = array();
        // Show custom Default Avatar
        if(!empty($wpua_avatar_default) && $wpua_functions->wpua_attachment_is_image($wpua_avatar_default)) {
          // Get image
          $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, array($size,$size));
          // Image src
          $default = $wpua_avatar_default_image[0];
          // Add dimensions if numeric size
          $default_image_details['dimensions'] = ' width="'.$wpua_avatar_default_image[1].'" height="'.$wpua_avatar_default_image[2].'"';
        
        } else {
          // Get mustache image based on numeric size comparison
          if($size >= 100) {
            $default = $mustache_original;
          } elseif($size < 100 && $size >= 60) {
            $default = $mustache_medium;
          } elseif($size < 60 && $size >= 50) {
            $default = $mustache_thumbnail;
          } elseif($size < 50 && $size >= 40) {
            $default = $mustache_avatar;
          } elseif($size < 40) {
            $default = $mustache_admin;
          }
          // Add dimensions if numeric size
          $default_image_details['dimensions'] = ' width="'.$size.'" height="'.$size.'"';
        }
        // Construct the img tag
        $default_image_details['size'] = $size;
        $default_image_details['src'] = $default;
         return $default_image_details;

  }
  /**
   * Replace get_avatar only in get_wp_user_avatar
   * @return string $avatar
   */
  public function wpua_get_avatar_filter($avatar, $id_or_email="", $size="", $default="", $alt="") {
    
    global $avatar_default, $wpua_functions;
    // User has WPUA

    if( $alt == '' ) $alt = 'Avatar';
    $avatar = str_replace('gravatar_default','',$avatar);
    if(is_object($id_or_email)) {
      if(!empty($id_or_email->comment_author_email)) {
        $avatar = get_wp_user_avatar($id_or_email, $size, $default, $alt);
      } else {

        $avatar = get_wp_user_avatar('unknown@gravatar.com', $size, $default, $alt);
      }
    } else {
      if(has_wp_user_avatar($id_or_email)) {
        $avatar = get_wp_user_avatar($id_or_email, $size, $default, $alt);
      // User doesn't have WPUA and Default Avatar is wp_user_avatar, show custom Default Avatar
      } elseif($avatar_default == 'wp_user_avatar') {

       $default_image_details = $this->wpua_default_image($size); 
       $avatar = '<img src="'.$default_image_details['src'].'"'.$default_image_details['dimensions'].' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.' photo avatar-default" />';

       return $avatar;
        
         }
    }
    return $avatar;
  }

  /**
   * Get original avatar, for when user removes wp_user_avatar
   * @return string $default
   */
  public function wpua_get_avatar_original($id_or_email="", $size="", $default="", $alt="") {
    global $mustache_original, $wpua_avatar_default, $wpua_functions;
    // Remove get_avatar filter
    remove_filter('get_avatar', array($wpua_functions, 'wpua_get_avatar_filter'));
    if(!empty($wpua_avatar_default) && $wpua_functions->wpua_attachment_is_image($wpua_avatar_default)) {
        $size_numeric_w_x_h = array( get_option( $size . '_size_w' ), get_option( $size . '_size_h' ) );
        $wpua_avatar_default_image = $wpua_functions->wpua_get_attachment_image_src($wpua_avatar_default, $size_numeric_w_x_h);
        $default = $wpua_avatar_default_image[0];
    } else {
      $default = $mustache_original;
    }
    // Enable get_avatar filter
    add_filter('get_avatar', array($wpua_functions, 'wpua_get_avatar_filter'), 10, 5);
    /**
     * Filter original avatar src
     * @since 1.9
     * @param string $default
     */
    return $default;
  }
  /**
   * Find WPUA, show get_avatar if empty
   * @return string $avatar
   */
  public function get_wp_user_avatar($id_or_email="", $size='96', $align="", $alt="") {
    global $blog_id, $wpdb, $wpua_functions, $_wp_additional_image_sizes;
    $email='unknown@gravatar.com';
    // Checks if comment 
    
    if( $alt == '' ) $alt = 'Avatar';
    if(is_object($id_or_email)) {
      // Checks if comment author is registered user by user ID
      if($id_or_email->user_id != 0) {
        $email = $id_or_email->user_id;
      // Checks that comment author isn't anonymous
      } elseif(!empty($id_or_email->comment_author_email)) {
        // Checks if comment author is registered user by e-mail address
        $user = get_user_by('email', $id_or_email->comment_author_email);
        // Get registered user info from profile, otherwise e-mail address should be value
        $email = !empty($user) ? $user->ID : $id_or_email->comment_author_email;
      }
      $alt = $id_or_email->comment_author;
    } else {
      if(!empty($id_or_email)) {
        // Find user by ID or e-mail address
        $user = is_numeric($id_or_email) ? get_user_by('id', $id_or_email) : get_user_by('email', $id_or_email);
      } else {
        // Find author's name if id_or_email is empty
        $author_name = get_query_var('author_name');
        if(is_author()) {
          // On author page, get user by page slug
          $user = get_user_by('slug', $author_name);
        } else {
          // On post, get user by author meta
          $user_id = get_the_author_meta('ID');
          $user = get_user_by('id', $user_id);
        }
      }
      // Set user's ID and name
      if(!empty($user)) {
        $email = $user->ID;
        $alt = $user->display_name;
      }
    }
    // Checks if user has WPUA
    $wpua_meta = get_the_author_meta($wpdb->get_blog_prefix($blog_id).'user_avatar', $email);
    // Add alignment class
    $alignclass = !empty($align) && ($align == 'left' || $align == 'right' || $align == 'center') ? ' align'.$align : ' alignnone';
    // User has WPUA, check if on excluded list and bypass get_avatar
    if(!empty($wpua_meta) && $wpua_functions->wpua_attachment_is_image($wpua_meta)) {
      // Numeric size use size array
      $get_size = is_numeric($size) ? array($size,$size) : $size;
      // Get image src
      $wpua_image = $wpua_functions->wpua_get_attachment_image_src($wpua_meta, $get_size);
      // Add dimensions to img only if numeric size was specified
      $dimensions = is_numeric($size) ? ' width="'.$wpua_image[1].'" height="'.$wpua_image[2].'"' : "";
      // Construct the img tag
      $avatar = '<img src="'.$wpua_image[0].'"'.$dimensions.' alt="'.$alt.'" class="avatar avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.$alignclass.' photo" />';
    } else {
      // Check for custom image sizes
      $all_sizes = array_merge(get_intermediate_image_sizes(), array('original'));
      if(in_array($size, $all_sizes)) {
        if(in_array($size, array('original', 'large', 'medium', 'thumbnail'))) {
          $get_size = ($size == 'original') ? get_option('large_size_w') : get_option($size.'_size_w');
        } else {
          $get_size = $_wp_additional_image_sizes[$size]['width'];
        }
      } else {
        // Numeric sizes leave as-is
        $get_size = $size;
      }
      // User with no WPUA uses get_avatar
      $avatar = get_avatar($email, $get_size, $default="", $alt="");
      // Remove width and height for non-numeric sizes
      if(in_array($size, array('original', 'large', 'medium', 'thumbnail'))) {
        $avatar = preg_replace('/(width|height)=\"\d*\"\s/', "", $avatar);
        $avatar = preg_replace("/(width|height)=\'\d*\'\s/", "", $avatar);
      }
      $replace = array('wp-user-avatar ', 'wp-user-avatar-'.$get_size.' ', 'wp-user-avatar-'.$size.' ', 'avatar-'.$get_size, ' photo');
      $replacements = array("", "", "", 'avatar-'.$size.' wp-user-avatar wp-user-avatar-'.$size.$alignclass.' photo');
      $avatar = str_replace($replace, $replacements, $avatar);
    }
    return $avatar;
  }

  /**
   * Return just the image src
   * @since 1.1
   * @param int|string $id_or_email
   * @param int|string $size
   * @param string $align
   * @uses get_wp_user_avatar()
   * @return string
   */
  public function get_wp_user_avatar_src($id_or_email="", $size="", $align="") {
    $wpua_image_src = "";
    // Gets the avatar img tag
    $wpua_image = get_wp_user_avatar($id_or_email, $size, $align);
    // Takes the img tag, extracts the src
    if(!empty($wpua_image)) {
      $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $wpua_image, $matches, PREG_SET_ORDER);
      $wpua_image_src = !empty($matches) ? $matches [0] [1] : "";
    }
    return $wpua_image_src;
  }
}

/**
 * Initialize
 * @since 1.9.2
 */
function wpua_functions_init() {
  global $wpua_functions;
  $wpua_functions = new WP_User_Avatar_Functions();
}
add_action('plugins_loaded', 'wpua_functions_init');
