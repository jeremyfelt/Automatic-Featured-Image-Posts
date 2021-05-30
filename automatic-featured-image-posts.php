<?php
/**
 * Plugin Name:     Automatic Featured Image Posts
 * Plugin URI:      https://github.com/jeremyfelt/automatic-featured-image-posts
 * Description:     Automatically creates a new post with an assigned featured image from every image upload.
 * Author:          jeremyfelt
 * Author URI:      https://jeremyfelt.com
 * Text Domain:     automatic-featured-image-posts
 * Domain Path:     /languages
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

require_once __DIR__ . '/includes/automatic-featured-image-posts.php';
