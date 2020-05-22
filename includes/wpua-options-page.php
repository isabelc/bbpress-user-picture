<?php
/**
 * Admin page to change plugin options.
 *
 * @package WP User Avatar
 * @version 1.9.13
 */

global $show_avatars, $wpua_admin, $wpua_edit_avatar, $wpua_subscriber, $wpua_upload_size_limit;
$updated = false;
if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
  $updated = true;
}
$server_upload_size_limit = wp_max_upload_size();
// Convert to KB
if($server_upload_size_limit > 1024) {
  $server_upload_size_limit /= 1024;
}
$server_upload_size_limit = (int) $server_upload_size_limit;

$wpua_resize_crop = get_option('wp_user_avatar_resize_crop');
// Convert to KB
if($wpua_upload_size_limit > 1024) {
  $wpua_upload_size_limit /= 1024;
}
$wpua_upload_size_limit_with_units = (int) $wpua_upload_size_limit.'KB';
$wpua_resize_upload = get_option('wp_user_avatar_resize_upload');
$hide_resize = (bool) $wpua_resize_upload != 1 ? ' style="display:none;"' : "";
?>
<div class="wrap">
  <h2>bbPress User Picture</h2>
  <table><tr valign="top">
    <td align="top">
  <form method="post" action="<?php echo admin_url('options.php'); ?>">
    <?php settings_fields('wpua-settings-group'); ?>
    <?php do_settings_fields('wpua-settings-group', "");
      // Format settings in table
      $wpua_subscriber_settings = array();
      $wpua_subscriber_settings['subscriber-settings'] = '<div id="wpua-contributors-subscribers">
        <table class="form-table">
          <tr valign="top">
            <th scope="row">
              <label for="wp_user_avatar_upload_size_limit">'
                .__('Upload Size Limit', 'wp-user-avatar').' '.__('(only for Contributors & Subscribers)', 'wp-user-avatar').'
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>'.__('Upload Size Limit', 'wp-user-avatar').' '. __('(only for Contributors & Subscribers)', 'wp-user-avatar').'</span></legend>
                <input name="wp_user_avatar_upload_size_limit" type="text" id="wp_user_avatar_upload_size_limit" value="'.$wpua_upload_size_limit.'" class="regular-text" />
                <span id="wpua-readable-size">'.$wpua_upload_size_limit_with_units.'</span>
                <span id="wpua-readable-size-error">'.sprintf(__('%s exceeds the maximum upload size for this site.','wp-user-avatar'), "").'</span>
                <div id="wpua-slider"></div>
                <span class="description">'.sprintf('Maximum upload file size: %dKB.', $server_upload_size_limit).'</span>
              </fieldset>
              <fieldset>
                <label for="wp_user_avatar_edit_avatar">
                  <input name="wp_user_avatar_edit_avatar" type="checkbox" id="wp_user_avatar_edit_avatar" value="1" '.checked($wpua_edit_avatar, 1, 0).' />'
                  .__('Allow users to edit avatars', 'wp-user-avatar').'
                </label>
              </fieldset>
              <fieldset>
                <label for="wp_user_avatar_resize_upload">
                  <input name="wp_user_avatar_resize_upload" type="checkbox" id="wp_user_avatar_resize_upload" value="1" '.checked($wpua_resize_upload, 1, 0).' />'
                  .__('Resize avatars on upload', 'wp-user-avatar').'
                </label>
              </fieldset>
              <fieldset id="wpua-resize-sizes"'.$hide_resize.'>
                <label for="wp_user_avatar_resize_w">'.__('Width','wp-user-avatar').'</label>
                <input name="wp_user_avatar_resize_w" type="number" step="1" min="0" id="wp_user_avatar_resize_w" value="'.get_option('wp_user_avatar_resize_w').'" class="small-text" />
                <label for="wp_user_avatar_resize_h">'.__('Height','wp-user-avatar').'</label>
                <input name="wp_user_avatar_resize_h" type="number" step="1" min="0" id="wp_user_avatar_resize_h" value="'.get_option('wp_user_avatar_resize_h').'" class="small-text" />
                <br />
                <input name="wp_user_avatar_resize_crop" type="checkbox" id="wp_user_avatar_resize_crop" value="1" '.checked('1', $wpua_resize_crop, 0).' />
                <label for="wp_user_avatar_resize_crop">'.__('Crop avatars to exact dimensions', 'wp-user-avatar').'</label>
              </fieldset>
            </td>
          </tr>
        </table>
      </div>';

      echo implode("", $wpua_subscriber_settings);
    ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Avatar Display</th>
      <td>
        <fieldset>
          <legend class="screen-reader-text"><span>Avatar Display</span></legend>
          <label for="show_avatars">
          <input type="checkbox" id="show_avatars" name="show_avatars" value="1" <?php checked($show_avatars, 1); ?> />
          Show Avatars </label>
        </fieldset>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Default Avatar </th>
        <td class="defaultavatarpicker">
          <fieldset>
            <legend class="screen-reader-text"><span>Default Avatar</span></legend>For users without a custom avatar of their own, you can display a generic logo.<br />
            <?php echo $wpua_admin->wpua_add_default_avatar(); ?>
          </fieldset>
        </td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
  
</td>
  </tr></table>
</div>
