=== bbPress User Picture ===

Contributors: properfraction, collizo4sky, isabel104
Tags: user picture, bbPress, bbPress picture, user image, user photo, user avatar
Requires at least: 4.0
Tested up to: 5.4.1
Requires PHP: 7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Let bbPress forum users upload their own profile image. Disables Gravatar images to use only local images.

== Description ==

WordPress currently only allows you to use custom avatars that are uploaded through [Gravatar](http://gravatar.com/). This plugin lets your bbPress forum members upload their own image to use as a profile picture.

**Additional features:**

* Upload your own Default Avatar on the Avatar settings page.
* You can limit upload file size and image dimension for Contributors and Subscribers.

== Installation ==

1. Download, install, and activate the bbPress User Picture plugin.
2. On your profile edit page, click "Edit Image".
3. Choose an image, then click "Select Image".
4. Click "Update Profile".
5. Upload your own Default Avatar in the Avatar settings admin page (optional).
6. Choose a theme that has avatar support. In your theme, manually replace <code>get_avatar</code> with <code>get_wp_user_avatar</code> or leave <code>get_avatar</code> as-is.

**Example Usage**

= Posts =

Within [The Loop](http://codex.wordpress.org/The_Loop), you may be using:

`<?php echo get_avatar(get_the_author_meta('ID'), 96); ?>`

Replace this function with:

`<?php echo get_wp_user_avatar(get_the_author_meta('ID'), 96); ?>`

You can also use the values "original", "large", "medium", or "thumbnail" for your avatar size:

`<?php echo get_wp_user_avatar(get_the_author_meta('ID'), 'medium'); ?>`

You can also add an alignment of "left", "right", or "center":

`<?php echo get_wp_user_avatar(get_the_author_meta('ID'), 96, 'left'); ?>`

= Author Page =

On an author page outside of [The Loop](http://codex.wordpress.org/The_Loop), you may be using:

`<?php
  $user = get_user_by('slug', $author_name); 
  echo get_avatar($user->ID, 96);
?>`

Replace this function with:

`<?php
  $user = get_user_by('slug', $author_name);
  echo get_wp_user_avatar($user->ID, 96);
?>`

If you leave the options blank, WP User Avatar will detect whether you're inside [The Loop](http://codex.wordpress.org/The_Loop) or on an author page and return the correct avatar in the default 96x96 size:

`<?php echo get_wp_user_avatar(); ?>`

The function <code>get_wp_user_avatar</code> can also fall back to <code>get_avatar</code> if there is no WP User Avatar image. For this to work, "Show Avatars" must be checked in your WP User Avatar settings. When this setting is enabled, you will see the user's [Gravatar](http://gravatar.com/) avatar or Default Avatar.

= Comments =

For comments, you might have in your template:

`<?php echo get_avatar($comment, 32); ?>`

Replace this function with:

`<?php echo get_wp_user_avatar($comment, 32); ?>`

For comments, you must specify the $comment variable.

**Other Available Functions**

= get_wp_user_avatar_src =

Works just like <code>get_wp_user_avatar</code> but returns just the image src. This is useful if you would like to link a thumbnail-sized avatar to a larger version of the image:

`<a href="<?php echo get_wp_user_avatar_src($user_id, 'large'); ?>">
  <?php echo get_wp_user_avatar($user_id, 'thumbnail'); ?>
</a>`

= has_wp_user_avatar =

Returns true if the user has a WP User Avatar image. You must specify the user ID:

`<?php
  if ( has_wp_user_avatar($user_id) ) {
    echo get_wp_user_avatar($user_id, 96);
  } else {
    echo '<img src="my-alternate-image.jpg" />';
  }
?>`

== Frequently Asked Questions ==

= How do I use WP User Avatar? =

First, choose a theme that has avatar support. In your theme, you have a choice of manually replacing <code>get_avatar</code> with <code>get_wp_user_avatar</code>, or leaving <code>get_avatar</code> as-is. Here are the differences:

= get_wp_user_avatar =

1. Allows you to use the values "original", "large", "medium", or "thumbnail" for your avatar size.
2. Doesn't add a fixed width and height to the image if you use the aforementioned values. This will give you more flexibility to resize the image with CSS.
3. Allows you to use custom image sizes registered with [<code>add_image_size</code>](http://codex.wordpress.org/Function_Reference/add_image_size) (fixed width and height are added to the image).
4. Optionally adds CSS classes "alignleft", "alignright", or "aligncenter" to position your avatar.
5. Shows nothing if the user has no WP User Avatar image.
6. Shows the user's [Gravatar](http://gravatar.com/) avatar or Default Avatar only if "Show Avatars" is enabled in your WP User Avatar settings.

= get_avatar =

1. Requires you to enable "Show Avatars" in your WP User Avatar settings to show any avatars.
2. Accepts only numeric values for your avatar size.
3. Always adds a fixed width and height to your image. This may cause problems if you use responsive CSS in your theme.
4. Shows the user's [Gravatar](http://gravatar.com/) avatar or Default Avatar if the user doesn't have a WP User Avatar image. (Choosing "Blank" as your Default Avatar still generates a transparent image file.)
5. Requires no changes to your theme files if you are currently using <code>get_avatar</code>.

[Read more about get_avatar in the WordPress Function Reference](http://codex.wordpress.org/Function_Reference/get_avatar).

= Can I create a custom Default Avatar? =

In your WP User Avatar settings, you can upload your own Default Avatar.

= Can Contributors or Subscribers choose their own WP User Avatar image? =

Yes. These users will see a slightly different interface because they are allowed only one image upload.

= Will WP User Avatar work with comment author avatars? =

Yes, for registered users. Non-registered comment authors will show the Default Avatar.

= Will WP User Avatar work with BuddyPress? =

No, BuddyPress has its own custom avatar functions and WP User Avatar will override only some of them. It's best to use BuddyPress without WP User Avatar.

= How can I see which users have an avatar? =

For Administrators, WP User Avatar adds a column with avatar thumbnails to your Users list table. If "Show Avatars" is enabled in your WP User Avatar settings, you will see avatars to the left of each username instead of in a new column.

= What other functions are available for WP User Avatar? =

* <code>get_wp_user_avatar_src</code>: retrieves just the image URL
* <code>has_wp_user_avatar</code>: checks if the user has a WP User Avatar image
* [See example usage here](http://wordpress.org/extend/plugins/wp-user-avatar/installation/)

== Changelog ==

= 1.0 =
* Initial release, forked from WP User Avatar and modified.

== Upgrade Notice ==

= 1.0 =
* New 
