<?php
/* Uninstall file for the Automatic Featured Image Posts plugin.
 *
 * 2 options are added for Automatic Featured Image Posts
 *
 * afip_options controlled the post status and post type
 * afip_upgrade_check helped performer smoother upgrades
 *
 * Both are deleted when the plugin is uninstalled.
*/
if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option( 'afip_options' );
delete_option( 'afip_upgrade_check' );