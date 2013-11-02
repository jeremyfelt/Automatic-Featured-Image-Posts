<?php
/*
Plugin Name: Automatic Featured Image Posts
Plugin URI: http://jeremyfelt.com/wordpress/plugins/automatic-featured-image-posts/
Description: Automatically creates a new post with an assigned featured image from every image upload.
Version: 1.0
Author: Jeremy Felt
Author URI: http://jeremyfelt.com/
License: GPLv2
*/

/*  Copyright 2011-2013 Jeremy Felt (email: jeremy.felt@gmail.com)

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

class Automatic_Featured_Image_Posts_Foghlaim {

	/**
	 * @var string The default post type created.
	 */
	var $post_type = 'post';

	/**
	 * @var string The default post status used.
	 */
	var $post_status = 'draft';

	/**
	 * @var string The default post format used.
	 */
	var $post_format = 'standard';

	/**
	 * Setup the hooks used by the plugin.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_action( 'admin_init',          array( $this, 'upgrade_check'           )        );
		add_action( 'admin_init',          array( $this, 'add_languages'           )        );
		add_action( 'admin_menu',          array( $this, 'add_settings'            )        );
		add_action( 'admin_init',          array( $this, 'register_settings'       )        );
		add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 2 );
		add_action( 'add_attachment',      array( $this, 'create_post_from_image'  ), 20    );
	}

	/**
	 * Upon activation, we'll want to check for existing plugin options and make sure that
	 * defaults are assigned if this is the first time.
	 */
	public function activate() {
		$current_afip_options = get_option( 'afip_options' );
		$afip_options = array();

		if ( empty( $current_afip_options['default_post_status'] ) )
			$afip_options['default_post_status'] = $this->post_status;
		else
			$afip_options['default_post_status'] = $current_afip_options['default_post_status'];

		if ( empty( $current_afip_options['default_post_type'] ) )
			$afip_options['default_post_type'] = $this->post_type;
		else
			$afip_options['default_post_type'] = $current_afip_options['default_post_type'];

		if ( empty( $current_afip_options['default_post_format'] ) )
			$afip_options['default_post_format'] = $this->post_format;
		else
			$afip_options['default_post_format'] = $current_afip_options['default_post_format'];

		update_option( 'afip_options', $afip_options );

		if ( '0.5' != get_option( 'afip_upgrade_check' ) )
			update_option( 'afip_upgrade_check', '0.5' );
	}

	/**
	 * Make sure that we're on the current set of options by checking the version flag and then
	 * running through activation accordingly.
	 */
	public function upgrade_check() {
		if ( '0.5' != get_option( 'afip_upgrade_check' ) )
			$this->activate();
	}

	/**
	 * Add the text domain for plugin translation
	 */
	public function add_languages() {
		load_plugin_textdomain( 'automated-featured-image-posts', false, basename( dirname( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Add our settings to the admin menu.
	 *
	 * Add the sub-menu item under the Settings top-level menu. Of course, since I have to pick
	 * such a long plugin name, we'll shorten this to Auto Image Posts instead.
	 */
	public function add_settings() {
		add_options_page( __('Auto Image Posts', 'automatic-featured-image-posts' ), __('Auto Image Posts', 'automatic-featured-image-posts'), 'manage_options', 'automatic-featured-image-posts-settings', array( $this, 'view_settings' ) );
	}

	/**
	 * Callback function to display main settings view for Automatic Featured Image Posts
	 *
	 * Additional checks are now implemented for post-formats and post-thumbnails. If a user's theme
	 * does not support post thumbnails (featured images), we output a warning so that they are aware
	 * of the possible incompatibility. If a user's theme supports post formats, we give an extra option
	 * allowing them to choose which format to publish under.
	 */
	public function view_settings() {
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"></div>
			<h2><?php _e( 'Automatic Featured Image Posts', 'automatic-featured-image-posts' ); ?></h2>
			<h3><?php _e( 'Overview', 'automatic-featured-image-posts' ); ?></h3>
			<p style="margin-left: 12px;max-width: 640px;"><?php _e('The default <strong>post status</strong> is set to <strong>publish</strong> by default. This means that as soon as you upload a new image through any interface in WordPress, a new post will appear with that image assigned as the featured image.', 'automatic-featured-image-posts' ); ?></p>
			<p style="margin-left: 12px;max-width: 640px;"><?php _e('The default <strong>post type</strong> is set to the most familiar WordPress post type, <strong>Post</strong>. Other custom post types registered by your theme and installed plugins have been automatically detected and will also appear in the drop down menu as options. Note that these custom post types should have support for featured images, or they may not appear as you would like.', 'automatic-featured-image-posts' ); ?></p>
			<?php if ( current_theme_supports( 'post-formats' ) ) : ?><p style="margin-left: 12px;max-width: 640px;"><?php _e( 'Your theme supports <strong>post formats</strong>, so an extra option is available to set which format an automatically created post will be set to. The default is <strong>Standard</strong>. Note that the final display will depend on what is provided by your theme.', 'automatic-featured-image-posts' ); ?></p><?php endif; ?>
			<?php if ( ! current_theme_supports( 'post-thumbnails' ) ) : ?><div class="error" style="margin-left: 12px;max-width: 640px;"><?php _e( '<strong>PLEASE NOTE:</strong> Your current theme does <strong>NOT</strong> support featured images and thus this plugin will be severely limited. Images will be attached to posts after upload, but it may be impossible to see this until featured image support is added to your theme.', 'automatic-featured-image-posts' ); ?></div><?php endif; ?>
			<form method="POST" action="options.php">
		<?php
			settings_fields( 'afip_options' );
			do_settings_sections( 'afip' );
		?>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'automatic-featured-image-posts' ); ?>"></p>
			</form>
		</div>
		<?php
	}

	/**
	 * Register our settings and add the settings sections and fields.
	 */
	public function register_settings() {
		register_setting( 'afip_options', 'afip_options', array( $this, 'validate_options' ) );

		add_settings_section( 'afip_section_main', '', array( $this, 'output_section_text' ), 'afip' );

		add_settings_field( 'afip_default_post_status', __( 'Default Post Status:', 'automatic-featured-image-posts' ), array( $this, 'output_default_post_status_text' ), 'afip', 'afip_section_main' );
		add_settings_field( 'afip_default_post_type',   __( 'Default Post Type:',   'automatic-featured-image-posts' ), array( $this, 'output_default_post_type_text'   ), 'afip', 'afip_section_main' );

		if ( current_theme_supports( 'post-formats' ) )
			add_settings_field( 'afip_default_post_format', __( 'Default Post Format:', 'automatic-featured-image-posts' ), array( $this, 'output_default_post_format_text' ), 'afip', 'afip_section_main' );
	}

	/**
	 * Placeholder for settings section. Would be overkill methinks.
	 */
	public function output_section_text() { }

	/**
	 * Output the text related to selecting the post type to be assigned when a featured
	 * image is automatically added.
	 */
	public function output_default_post_type_text() {
		$afip_options = get_option( 'afip_options' );
		$all_post_types = get_post_types( array( '_builtin' => false ) );

		if ( ! isset( $afip_options['default_post_type'] ) )
			$afip_options['default_post_type'] = $this->post_type;
		?>
		<select id="afip-default-post-type" name="afip_options[default_post_type]">
			<option value="post" <?php selected( $afip_options['default_post_type'], 'post' ); ?>>Post</option>
		<?php foreach( $all_post_types as $p ) : ?>
			<option value="<?php echo esc_attr( $p ); ?>" <?php selected( $afip_options['default_post_type'], esc_attr( $p ) ); ?>><?php echo esc_html( $p ); ?></option>
		<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Output the text related to selecting the post status to be assigned when a featured
	 * image is automatically added.
	 */
	public function output_default_post_status_text() {
		$afip_options = get_option( 'afip_options' );

		if ( ! isset( $afip_options['default_post_status'] ) )
			$afip_options['default_post_status'] = $this->post_status;
		?>
		<select id="afip_default_post_status" name="afip_options[default_post_status]">
			<option value="draft"   <?php selected( $afip_options['default_post_status'], 'draft'   ); ?>>Draft</option>
			<option value="publish" <?php selected( $afip_options['default_post_status'], 'publish' ); ?>>Publish</option>
			<option value="private" <?php selected( $afip_options['default_post_status'], 'private' ); ?>>Private</option>
		</select>
		<?php
	}

	/**
	 * Output the text related to selecting the post formats to be assigned when a featured
	 * image is automatically added.
	 */
	public function output_default_post_format_text() {
		global $_wp_theme_features;
		$afip_options = get_option( 'afip_options' );

		if ( ! isset( $afip_options['default_post_format'] ) )
			$afip_options['default_post_format'] = $this->post_format;
		?>
		<select id="afip_default_post_format" name="afip_options[default_post_format]">
			<?php
				if ( isset( $_wp_theme_features['post-formats'] ) && is_array( $_wp_theme_features['post-formats'] ) ) {
					foreach ( $_wp_theme_features['post-formats'] as $post_format_array ){
						foreach ( $post_format_array as $post_format ) {
							?>
							<option value="<?php echo esc_attr( $post_format ); ?>" <?php selected( $afip_options['default_post_format'], esc_attr( $post_format ) ); ?>><?php echo esc_html( ucwords( $post_format ) ); ?></option>
							<?php
						}
						if ( ! in_array( 'standard', $post_format_array ) ) {
							?>
							<option value="standard" <?php selected( $afip_options['default_post_format'], 'standard' ); ?>>Standard</option>
							<?php
						}
					}
				} else {
					?>
					<option value="standard" <?php selected( $afip_options['default_post_format'], 'standard' ); ?>>Standard</option>
					<?php
				}
		?>
		</select>
		<?php
	}

	/**
	 * Validation of a drop down. Hmm. Well, if it isn't on our list, we'll force it onto our list.
	 *
	 * @param $input array of options that we are attempting to store to the database
	 * @return array of validated options that we've confirmed for storing
	 */
	public function validate_options( $input ) {
		global $_wp_theme_features;
		$valid_post_status_options = array( 'draft', 'publish', 'private' );
		$valid_post_type_options = get_post_types( array( '_builtin' => false ) );
		$valid_post_type_options[] = $this->post_type;
		$valid_post_format_options = array( $this->post_format );

		if ( isset( $_wp_theme_features['post-formats'] ) && is_array( $_wp_theme_features['post-formats'] ) ) {
			foreach ( $_wp_theme_features['post-formats'] as $post_format_array ) {
				foreach ( $post_format_array as $post_format ) {
					$valid_post_format_options[] = $post_format;
				}
			}
		}

		if ( ! in_array( $input['default_post_status'], $valid_post_status_options ) )
			$input['default_post_status'] = $this->post_status;

		if ( ! in_array( $input['default_post_type'], $valid_post_type_options ) )
			$input['default_post_type'] = $this->post_type;

		if ( isset( $input['default_post_format'] ) && ! in_array( $input['default_post_format'], $valid_post_format_options ) )
			$input['default_post_format'] = $this->post_format;

		return $input;
	}

	/**
	 * Make a pretty link for settings under the plugin information in the admin screen
	 *
	 * Function gratefully taken (and barely modified) from Pippin Williamson's
	 * WPMods article: http://www.wpmods.com/adding-plugin-action-links/
	 *
	 * @param $links array of links to be shown with the plugin
	 * @param $file string of the filename we're working with at the moment
	 * @return array Updated links to be shown with the plugin
	 */
	public function add_plugin_action_links( $links, $file ) {
		static $this_plugin;

		if ( ! $this_plugin )
			$this_plugin = plugin_basename( __FILE__ );

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . site_url( '/wp-admin/options-general.php?page=automatic-featured-image-posts-settings' ) . '">' . __( 'Settings', 'automatic-featured-image-posts' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Hook into the add_attachment action, as this should occur after the image has uploaded and after meta data
	 * about the image has been saved. We previously tried using the filter wp_update_attachment_metadata instead,
	 * but that just doesn't seem like the right choice.
	 *
	 * Pulling out extra EXIF areas now, as we weren't really doing anything extra with that, just relying on the
	 * data that WordPress already uses. May be fun to revisit sometime though to allow for more data from Lightroom, etc.
	 *
	 * @param int $post_id The post ID of the attachment that was just added from an image upload.
	 *
	 * @return mixed Only used when breaking from the script
	 */
	public function create_post_from_image( $post_id ) {

		if( ! wp_attachment_is_image( $post_id ) )
			return;

		$new_post_category = array();

		// If an image is being uploaded through an existing post, it will have been assigned a post parent
		if ( $parent_post_id = get_post( $post_id )->post_parent ) {

			/**
			 * It doesn't make sense to create a new post with a featured image from an image
			 * uploaded to an existing post. By default, we'll return having done nothing if
			 * it is detected that this image already has a post parent. The filter allows a
			 * plugin or theme to make a different decision here.
			 */
			if ( false === apply_filters( 'afip_post_parent_continue', false, $post_id, $parent_post_id ) )
				return;

			/**
			 * If this image is being added through an existing post, make sure that it inherits
			 * the category setting from its parent.
			 */
			if ( $parent_post_categories = get_the_category( $parent_post_id ) ) {
				foreach( $parent_post_categories as $post_cat )
					$new_post_category[] = $post_cat->cat_ID;
			}
		}

		$afip_options = get_option( 'afip_options' );
		$current_user = wp_get_current_user();

		/* Allow other functions or themes to change the post date before creation. */
		$new_post_date = apply_filters( 'afip_new_post_date', current_time( 'mysql' ), $post_id );

		/* Allow other functions or themes to change the post title before creation. */
		$new_post_title = apply_filters( 'afip_new_post_title', get_the_title( $post_id ), $post_id );

		/* Allow other functions or themes to change the post categories before creation. */
		$new_post_category = apply_filters( 'afip_new_post_category', $new_post_category, $post_id );

		/* Allow other functions or themes to change the post content before creation. */
		$new_post_content = apply_filters( 'afip_new_post_content', '', $post_id );

		// Provide a filter to bail before post creation for certain post IDs.
		if ( false === apply_filters( 'afip_continue_new_post', true, $post_id ) )
			return;

		// Allow others to hook in and perform an action before a post is created.
		do_action( 'afip_pre_create_post', $post_id );

		$new_post_id = wp_insert_post( array(
			'post_title'    => $new_post_title,
			'post_content'  => $new_post_content,
			'post_status'   => $afip_options['default_post_status'],
			'post_author'   => $current_user->ID,
			'post_date'     => $new_post_date,
			'post_category' => $new_post_category,
			'post_type'     => $afip_options['default_post_type'],
		));

		if ( isset( $afip_options['default_post_format'] ) && 'standard' !== $afip_options['default_post_format'] )
			set_post_format( $new_post_id, $afip_options['default_post_format'] );

		update_post_meta( $new_post_id, '_thumbnail_id', $post_id );

		// Update the original image (attachment) to reflect new status.
		wp_update_post( array(
			'ID'          => $post_id,
			'post_parent' => $new_post_id,
			'post_status' => 'inherit',
		) );

		/**
		 * Allow others to hook in and perform an action as each operation is complete. Passes
		 * $new_post_id from the newly created post and $post_id representing the image.
		 */
		do_action( 'afip_created_post', $new_post_id, $post_id );
	}
}
new Automatic_Featured_Image_Posts_Foghlaim();