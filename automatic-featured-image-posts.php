<?php
/*
Plugin Name: Automatic Featured Image Posts
Plugin URI: http://www.jeremyfelt.com/wordpress/plugins/automatic-featured-image-posts/
Description: Automatically creates a new post with an assigned featured image from every image upload.
Version: 0.2
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

register_activation_hook( __FILE__, 'afip_activate' );

if ( is_admin() ){
	/*  We added an option in version 0.3, need to create it on upgrade */
	add_action( 'admin_init', 'afip_check_and_upgrade' );
    /*  Add our settings to the admin menu. */
    add_action( 'admin_menu', 'afip_add_settings' );
    /*  Register the settings. */
    add_action( 'admin_init', 'afip_register_settings' );
    /*  Make a pretty link for settings under the plugin information. */
    add_filter( 'plugin_action_links', 'afip_plugin_action_links', 10, 2);
}

/*  Hook into the wp_update_attachment_metadata filter, which occurs after
    the attachment has been uploaded and meta data saved. */
add_filter( 'wp_update_attachment_metadata', 'afip_create_post_from_image', 10, 2 );

function afip_check_and_upgrade(){
	if ( ! get_option( 'afip_upgrade_check' ) )
		afip_activate();
}

function afip_add_languages(){
    /*  Add language data for the plugin. */
    $plugin_dir = basename( dirname( __FILE__ ) ) . '/lang';
    load_plugin_textdomain( 'automated-featured-image-posts', false, $plugin_dir );
}

function afip_activate(){
    /*  Upon activation, we'll want to check for existing options and make sure things
        are in their right place. */
    $current_afip_options = get_option( 'afip_options' );
    $afip_options[ 'default_post_status' ] = isset( $current_afip_options[ 'default_post_status' ] ) ? $current_afip_options[ 'default_post_status' ] : 'publish';
	$afip_options[ 'default_post_type' ] = isset( $current_afip_options[ 'default_post_type' ] ) ? $current_afip_options[ 'default_post_type' ] : 'post';
   	add_option( 'afip_options', $afip_options );
}

function afip_add_settings(){
    /*	Add the sub-menu item under the Settings top-level menu. Of course, since I have to pick
        such a long plugin name, we'll shorten this to Auto Image Posts instead. */
   	add_options_page( __('Auto Image Posts', 'automatic-featured-image-posts' ), __('Auto Image Posts', 'automatic-featured-image-posts'), 'manage_options', 'automatic-featured-image-posts-settings', 'afip_view_settings' );
}

function afip_view_settings(){
    /*	Display the main settings view for Custom Posts Per Page. */
   	echo '<div class="wrap">
   		<div class="icon32" id="icon-options-general"></div>
   			<h2>' . __( 'Automatic Featured Image Posts', 'automatic-featured-image-posts' ) . '</h2>
   			<h3>' . __( 'Overview', 'automatic-featured-image-posts' ) . ':</h3>
   			<p style="margin-left:12px;max-width:640px;">' . __( 'In this first release, the only setting available is for
   			what status the automatically created post will have after you upload an image. By default it is set to publish,
   			which means as soon as you upload a new image, a new post will appear. Options for draft and private are also
   			available, though it should be noted that using using bulk edit in WordPress to change the post status from draft
   			to publish seems to change the post date for all selected posts to your current time. You may want to use quick
   			edit or edit instead when changing statuses.', 'automatic-featured-image-posts' ) . '</p>';
       echo '<form method="post" action="options.php">';

   	settings_fields( 'afip_options' );
   	do_settings_sections( 'afip' ); // Display the main section of settings.

   	echo '<p class="submit"><input type="submit" class="button-primary" value="';
   	_e( 'Save Changes', 'automatic-featured-image-posts' );
   	echo '" />
   			</p>
   			</form>
   		</div>';
}

function afip_register_settings(){
    /*  Register the settings we want available for this. */
    register_setting( 'afip_options', 'afip_options', 'afip_options_validate' );
   	add_settings_section( 'afip_section_main', '', 'afip_section_text', 'afip' );
    add_settings_field( 'afip_default_post_status', __( 'Default Post Status:', 'automatic-featured-image-posts' ) , 'afip_default_post_status_text', 'afip', 'afip_section_main' );
	add_settings_field( 'afip_default_post_type', __( 'Default Post Type:', 'automatic-featured-image-posts' ), 'afip_default_post_type_text', 'afip', 'afip_section_main' );
}

function afip_section_text(){
    /*  Placeholder for later. Nothing really needed at the moment. */
}

function afip_default_post_type_text(){
	$afip_options = get_option( 'afip_options' );
	$all_post_types = get_post_types( array( '_builtin' => false ) );

	if ( ! isset( $afip_options[ 'default_post_type' ] ) )
		$afip_options[ 'default_post_type' ] = array( 'post' );

	echo '<select id="afip-default-post-type" name="afip_options[default_post_type]"><option value="post" ' . selected( $afip_options[ 'default_post_type' ], 'post', false ) . '>Post</option>';
	foreach ( $all_post_types as $p ) {
		echo '<option value="' . $p . '" ' . selected( $afip_options[ 'default_post_type' ], $p, false ) . '>' . $p . '</option>';
	}
	echo '</select>';
}

function afip_default_post_status_text(){
    $afip_options = get_option( 'afip_options' );

    /*  There's probably a better way to do this, but I'm tired and this works and I'll think of one later.
        I promise. */
    $s1 = '';
    $s2 = '';
    $s3 = '';

    if( 'draft' == $afip_options[ 'default_post_status' ] ){
        $s1 = 'selected="yes"';
    }elseif( 'publish' == $afip_options[ 'default_post_status' ] ){
        $s2 = 'selected="yes"';
    }elseif( 'private' == $afip_options[ 'default_post_status' ] ){
        $s3 = 'selected="yes"';
    }else{
        $s1 = 'selected="yes"';
    }

    echo '<select id="afip_default_post_status" name="afip_options[default_post_status]">
            <option value="draft" ' . $s1 . '>draft</option>
            <option value="publish" ' . $s2 . '>publish</option>
            <option value="private" ' . $s3 . '>private</option>
          </select>';
}

function afip_options_validate( $input ) {
    /*  Validation of a drop down. Hmm. Well, if it isn't on our list, we'll force it onto our list. */
    $valid_post_status_options = array( 'draft', 'publish', 'private' );
	$valid_post_type_options = get_post_types( array( '_builtin' => false ) );
	$valid_post_type_options[] = 'post';

    if( ! in_array( $input[ 'default_post_status' ], $valid_post_status_options ) )
        $input[ 'default_post_status' ] = 'draft';

	if( ! in_array( $input[ 'default_post_type' ], $valid_post_type_options ) )
		$input[ 'default_post_type' ] = 'post';

    return $input;
}

function afip_plugin_action_links( $links, $file ){
    /*  Function gratefully taken (and barely modified) from Pippin Williamson's
        WPMods article: http://www.wpmods.com/adding-plugin-action-links/ */
    static $this_plugin;

    if ( ! $this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }

    // check to make sure we are on the correct plugin
    if ( $file == $this_plugin ){
        $settings_path = '/wp-admin/options-general.php?page=automatic-featured-image-posts-settings';
        $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . $settings_path . '">' . __( 'Settings', 'automatic-featured-image-posts' ) . '</a>';
        array_unshift( $links, $settings_link );  // add the link to the list
    }

    return $links;
}

function afip_create_post_from_image( $data , $post_id ){

    /*  Check to see if this is an image, as that's all we work with currently. */
    if( ! wp_attachment_is_image( $post_id ) )
        return $data;

    /*  By default, we use a blank category array which gives us only the default category
        when the post is created. */
    $new_post_category = array();

    if ( get_post( $post_id )->post_parent ) {
        /*  TODO: Make this an option at some point. */
        /*  This image is being added through an existing post, so we'll grab existing category data. */
        $parent_post_id = get_post( $post_id )->post_parent;
        $parent_post_categories = get_the_category( $parent_post_id );
        if ( $parent_post_categories ) {
            foreach( $parent_post_categories as $post_cat ) {
                $new_post_category[] = $post_cat->cat_ID;
            }
        }
    }

    /*  Great! It is an image, process it fully. */
    $afip_options = get_option( 'afip_options' );

    /*  Create the post_date from the EXIF data in the post meta if possible. If a
        date doesn't exist, we'll just use the current. Also, since created_timestamp
        is saved in Unix time, we'll be lazy and compare it to 1. */
    if ( $data[ 'image_meta' ][ 'created_timestamp' ] > 1 ){
        $new_post_date = date( 'Y-m-d H:i:s', $data[ 'image_meta' ][ 'created_timestamp' ] );
    }else{
        $new_post_date = date( 'Y-m-d H:i:s' );
    }

    /*  The WordPress media library already pulls the title from image metadata upon initial
        upload, so we don't have to try to hard. We'll either get the file name or the title
        back and that will have to do. */
    $new_post_title = get_the_title( $post_id );

    /*  We'll want to make this a template in the future, ideally reading from some kind of Exif
        tag in the image itself, but for now we'll leave the content blank. */
    $new_post_content = '';

    /*  This will be an option in the future, but for now the default status is draft. */
    $new_post_status = $afip_options[ 'default_post_status' ];
	$new_post_type = $afip_options[ 'default_post_type' ];

    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;

    /*  Build the arguments for the new post being created. */
    $new_post_data = array(
        'post_title' => $new_post_title,
        'post_content' => $new_post_content,
        'post_status' => $new_post_status,
        'post_author' => $new_post_author,
        'post_date' => $new_post_date,
        'post_category' => $new_post_category,
	    'post_type' => $new_post_type,
    );

    /*  Insert the new post */
    $new_post_id = wp_insert_post( $new_post_data );

    /*  Assign the featured image */
    update_post_meta( $new_post_id, '_thumbnail_id', $post_id );

    return $data;

}