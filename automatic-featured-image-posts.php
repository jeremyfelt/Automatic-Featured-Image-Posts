<?php
/*
Plugin Name: Automatic Featured Image Posts
Plugin URI: http://www.jeremyfelt.com/wordpress/plugins/automatic-featured-image-posts/
Description: Automatically creates a new post with an assigned featured image from every image upload.
Version: 0.3
Author: Jeremy Felt
Author URI: http://www.jeremyfelt.com
License: GPL2
*/

/*  Copyright 2011 Jeremy Felt (email: jeremy.felt@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Upon activation, we'll want to check for existing options and make sure
 * things are in their right place. */
register_activation_hook( __FILE__, 'afip_activate' );
function afip_activate() {
	$current_afip_options = get_option( 'afip_options' );
	$afip_options[ 'default_post_status' ] = isset( $current_afip_options[ 'default_post_status' ] ) ? $current_afip_options[ 'default_post_status' ] : 'publish';
	$afip_options[ 'default_post_type' ] = isset( $current_afip_options[ 'default_post_type' ] ) ? $current_afip_options[ 'default_post_type' ] : 'post';
	add_option( 'afip_options', $afip_options );

	if ( ! get_option( 'afip_upgrade_check' ) )
		add_option( 'afip_upgrade_check', '0.3' );
}

/* We added an option in version 0.3, need to create
 * it on upgrade */
add_action( 'admin_init', 'afip_check_and_upgrade' );
function afip_check_and_upgrade() {
	if ( ! get_option( 'afip_upgrade_check' ) )
		afip_activate();
}

/*  Add language data for the plugin. */
add_action( 'admin_init', 'afip_add_languages' );
function afip_add_languages() {
	$plugin_dir = basename( dirname( __FILE__ ) ) . '/lang';
	load_plugin_textdomain( 'automated-featured-image-posts', false, $plugin_dir );
}

/* Add our settings to the admin menu.
 *
 * Add the sub-menu item under the Settings top-level menu. Of course, since I have to pick
 * such a long plugin name, we'll shorten this to Auto Image Posts instead.
*/
add_action( 'admin_menu', 'afip_add_settings' );
function afip_add_settings() {
	add_options_page( __('Auto Image Posts', 'automatic-featured-image-posts' ), __('Auto Image Posts', 'automatic-featured-image-posts'), 'manage_options', 'automatic-featured-image-posts-settings', 'afip_view_settings' );
}

/* Callback function to display main settings view for
 * Automatic Featred Image Posts */
function afip_view_settings() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"></div>
		<h2><?php _e( 'Automatic Featured Image Posts', 'automatic-featured-image-posts' ); ?></h2>
		<h3><?php _e( 'Overview', 'automatic-featured-image-posts' ); ?></h3>
		<p style="margin-left: 12px;max-width: 640px;"><?php _e( 'Two options are available to you with Automatic Featured Image Posts.', 'automatic-featured-image-posts' ); ?></p>
		<p style="margin-left: 12px;max-width: 640px;"><?php _e('Default Post Status is set to publish by default, which means that as soon as you upload a new image through	any interface in the WordPress admin pages, a new post will appear with that image assigned as the featured	image.', 'automatic-featured-image-posts' ); ?></p>
		<p style="margin-left: 12px;max-width: 640px;"><?php _e('The Default Post Type is set to the most familiar WordPress post type, post. The other custom post types installed on your site have been automatically detected and will appear in the drop down menu as options. Note that these custom post types should have support for featured images, or they may not appear as you would like.', 'automatic-featured-image-posts' ); ?></p>
		<form method="POST" action="options.php">
<?php
		settings_fields( 'afip_options' );
		do_settings_sections( 'afip' ); // Display the main section of settings.
?>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'automatic-featured-image-posts' ); ?>"></p>
		</form></div>
<?php
}

/*  Register the settings. */
add_action( 'admin_init', 'afip_register_settings' );
function afip_register_settings() {
	register_setting( 'afip_options', 'afip_options', 'afip_options_validate' );
	add_settings_section( 'afip_section_main', '', 'afip_section_text', 'afip' );
	add_settings_field( 'afip_default_post_status', __( 'Default Post Status:', 'automatic-featured-image-posts' ) , 'afip_default_post_status_text', 'afip', 'afip_section_main' );
	add_settings_field( 'afip_default_post_type', __( 'Default Post Type:', 'automatic-featured-image-posts' ), 'afip_default_post_type_text', 'afip', 'afip_section_main' );
}

function afip_section_text() {
	/*  Placeholder for later. Nothing really needed at the moment. */
}

function afip_default_post_type_text() {
	$afip_options = get_option( 'afip_options' );
	$all_post_types = get_post_types( array( '_builtin' => false ) );

	if ( ! isset( $afip_options[ 'default_post_type' ] ) )
		$afip_options[ 'default_post_type' ] = array( 'post' );
?>
	<select id="afip-default-post-type" name="afip_options[default_post_type]">
		<option value="post" <?php selected( $afip_options[ 'default_post_type' ], 'post' ); ?>>Post</option>
		<?php foreach( $all_post_types as $p ) : ?><option value="<?php echo $p; ?>" <?php selected( $afip_options[ 'default_post_type' ], $p ); ?>><?php echo $p; ?></option><?php endforeach; ?>
	</select>
<?php
}

function afip_default_post_status_text() {
	$afip_options = get_option( 'afip_options' );
?>
	<select id="afip_default_post_status" name="afip_options[default_post_status]">
		<option value="draft" <?php selected( $afip_options[ 'default_post_status' ], 'draft' ); ?>>draft</option>
		<option value="publish" <?php selected( $afip_options[ 'default_post_status' ], 'publish' ); ?>>publish</option>
		<option value="private" <?php selected( $afip_options[ 'default_post_status' ], 'private' ); ?>>private</option>
	</select>
<?php
}

/*  Validation of a drop down. Hmm. Well, if it isn't on our list, we'll force it onto our list. */
function afip_options_validate( $input ) {
	$valid_post_status_options = array( 'draft', 'publish', 'private' );
	$valid_post_type_options = get_post_types( array( '_builtin' => false ) );
	$valid_post_type_options[] = 'post';

	if( ! in_array( $input[ 'default_post_status' ], $valid_post_status_options ) )
		$input[ 'default_post_status' ] = 'draft';

	if( ! in_array( $input[ 'default_post_type' ], $valid_post_type_options ) )
		$input[ 'default_post_type' ] = 'post';

	return $input;
}

/*  Make a pretty link for settings under the plugin information. */
add_filter( 'plugin_action_links', 'afip_plugin_action_links', 10, 2 );
function afip_plugin_action_links( $links, $file ) {
	/*  Function gratefully taken (and barely modified) from Pippin Williamson's
		WPMods article: http://www.wpmods.com/adding-plugin-action-links/ */
	static $this_plugin;

	if ( ! $this_plugin )
		$this_plugin = plugin_basename( __FILE__ );

	if ( $file == $this_plugin ) {
		$settings_path = '/wp-admin/options-general.php?page=automatic-featured-image-posts-settings';
		$settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . $settings_path . '">' . __( 'Settings', 'automatic-featured-image-posts' ) . '</a>';
		array_unshift( $links, $settings_link );  // add the link to the list
	}
	return $links;
}

/* Hook into the add_attachment action, as this should occur after the image has uploaded
 * and after meta data about the image has been saved. We previously tried using the filter
 * wp_update_attachment_metadata instead, but that just doesn't seem like the right choice.
 *
 * Pulling out extra EXIF areas now, as we weren't really doing anything extra with that, just
 * relying on the data that WordPress already uses. May be fun to revisit sometime though to
 * allow for more data from Lightroom, etc.
*/
add_action( 'add_attachment', 'afip_create_post_from_image', 20 );
function afip_create_post_from_image( $post_id ) {
	/*  Check to see if this is an image, as that's all we work with currently. */
	if( ! wp_attachment_is_image( $post_id ) )
		return;

	/*  By default, we use a blank category array which gives us only the default category
		when the post is created. */
	$new_post_category = array();

	/*  This image is being added through an existing post, so we'll grab existing category data. */
	if ( $parent_post_id = get_post( $post_id )->post_parent ) {
		if ( $parent_post_categories = get_the_category( $parent_post_id ) ) {
			foreach( $parent_post_categories as $post_cat )
				$new_post_category[] = $post_cat->cat_ID;
		}
	}

	$afip_options = get_option( 'afip_options' );

	$new_post_date = date( 'Y-m-d H:i:s' );

	$current_user = wp_get_current_user();

	/*  Build the arguments for the new post being created. */
	$new_post_data = array(
		'post_title' => get_the_title( $post_id ),
		'post_content' => '',
		'post_status' => $afip_options[ 'default_post_status' ],
		'post_author' => $current_user->ID,
		'post_date' => $new_post_date,
		'post_category' => $new_post_category,
		'post_type' => $afip_options[ 'default_post_type' ],
	);

	/*  Insert the new post */
	$new_post_id = wp_insert_post( $new_post_data );
	/*  Assign the featured image */
	update_post_meta( $new_post_id, '_thumbnail_id', $post_id );
}