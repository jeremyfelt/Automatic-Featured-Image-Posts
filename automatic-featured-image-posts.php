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
 * License:         GPLv2
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
