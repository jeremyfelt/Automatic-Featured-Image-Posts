<?php
/**
 * Plugin Name:     Automatic Featured Image Posts
 * Plugin URI:      https://github.com/jeremyfelt/automatic-featured-image-posts
 * Description:     Automatically creates a new post with an assigned featured image from every image upload.
 * Author:          jeremyfelt
 * Author URI:      https://jeremyfelt.com
 * Version:         1.0.0
 * License:         GPLv2 or later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin, like WordPress, requires PHP 5.6 and higher.
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	add_action( 'admin_notices', 'automatic_featured_image_posts_admin_notice' );
	/**
	 * Display an admin notice if PHP is not 5.6.
	 */
	function automatic_featured_image_posts_admin_notice() {
		echo '<div class=\"error\"><p>';
		echo __( 'The Automatic Featured Image Posts WordPress plugin requires PHP 5.6 to function properly. Please upgrade PHP or deactivate the plugin.', 'automatic-featured-image-posts' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</p></div>';
	}

	return;
}

// Capture the filename so that it's easily available just in case for some reason
// someone changes this filename. How defensive is that!
define( 'AFIP_PLUGIN_FILE', plugin_basename( __FILE__ ) );

require_once __DIR__ . '/includes/automatic-featured-image-posts.php';
