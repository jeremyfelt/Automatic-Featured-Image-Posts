=== Automatic Featured Image Posts ===

Contributors: jeremyfelt
Donate link: http://jeremyfelt.com/wordpress/plugins/automatic-featured-image-posts/
Tags: featured image, media, photo, pictures, posts, photoblog, upload, automatic, custom post type, thumbnail, post thumbnails, post formats
Requires at least: 3.2.1
Tested up to: 3.7
Stable tag: 1.0
License: GPLv2

Automatic Featured Image Posts creates a new post with a Featured Image every time an image is uploaded.

== Description ==

Automatic Featured Image Posts creates a new post with a Featured Image every time an image is uploaded. Through the plugin settings page, you can set the image to publish and assign itself to one of your other existing custom post types and/or post formats.

The imagined use case is to make managing a large number of photos through WordPress a little more interesting and a little more fun.

After uploading 10, 100, or 1000 pictures from an event or vacation, you and other users can go through and spend the majority of your time adding content, tags, and titles to your photographs rather than going through a monotonous process creating new posts over and over again.

Settings are available for:

*  Default Post Status (draft, pending, published, private)
*  Default Post Type
    *  Default is the WordPress post.
    *  Can choose any custom post type registered in your WordPress installation.
*  Default Post Format
    *  Default is 'standard', which equates to none.
    *  Other options are provided if registered by your theme

Filters are available for:

* `afip_new_post_title` = Allow other functions or themes to change the post title before creation.
* `afip_new_post_category` = Allow other functions or themes to change the post categories before creation.
* `afip_new_post_content` = Allow other functions or themes to change the post content before creation.
* `afip_new_post_date` = Allow other functions or themes to change the post date before creation.
* `afip_post_parent_continue` = Allow creation of a new post when an image is inserted in an existing post.
* `afip_continue_new_post` = Allow other functions or themes to skip creation of a post.

Actions are available for:

* `afip_pre_create_post` = Runs immediately before each post is created for an image.
* `afip_created_post` = Runs after each image load is processed.

Feel free to [fork, submit issues, and/or contribute on GitHub](https://github.com/jeremyfelt/Automatic-Featured-Image-Posts)

== Installation ==

1.  Upload 'automatic-featured-image-posts' to your plugin directory, usually 'wp-content/plugins/', or install automatically via your WordPress admin page.
1.  Activate Automatic Featured Image Posts in your plugin menu.
1.  If you'd like to change the default post status or post type, configure the plugin using the Auto Image Posts menu under Settings in your admin page. (*See Screenshot*)

That's it!

== Frequently Asked Questions ==

= Why don't I see any of my images? The posts were created. =

*  More than likely the installed theme does not support featured images (post thumbnails). If this is the case, images are assigned to posts in the background, but there is no interface to display or edit them.
*  A warning will appear on your Automatic Featured Image Posts settings screen if it is detected that featured images are not enabled.

= Can you put the images in post content instead of setting them as a featured image? =

*  I can't, but you can with the included filters. Check out the [writeup on the new filters](http://jeremyfelt.com/wordpress/2012/04/14/filters-in-automatic-featured-image-posts "Filters in Automatic Featured Image Posts")

== Screenshots ==

1. An overview of the Automatic Featured Image Posts settings screen.

== Changelog ==

= 1.0 =
* Confirm compatibility with WordPress 3.7
* Introduce new action, `afip_pre_create_post`, to allow actions to occur immediately before inserting a new post for an image.
* Introduce new filter, `afip_continue_new_post`. Return false to skip creation of a post for a specific image.
* General code cleanup.

= 0.9 =
* Implement decision to skip images that are uploaded to existing posts
* Add filter for afip_post_parent_continue to allow overriding this decision
* Add action afip_created_post to run after each operation

= 0.8 =
* Compatibility check with upcoming WordPress 3.5, all is a go!
* Fix post time bug to account for WordPress timezone setting. props Matthew Harris
* Add filter to new post date so that it can be modified.
* General code style cleaning

= 0.7 =
* Fix a couple bugs with saving options when post formats aren't yet enabled.

= 0.6 =
*  Add filters to allow themes and plugins to change the post title, categories, and content before creation

= 0.5 =
*  Add support for Post Formats. If your theme supports it, you can now select to publish as an image, aside, etc...
*  Make sure the attachment is also adhering to core parent/inherit standards after media is uploaded.
*  Add a warning for themes that do not support featured images.
*  Code cleanup, specifically moving everything into its own class to be a good global namespace citizen.

= 0.4 =
*  Switched to use add_attachment action hook, possible avoidance of issues that I couldn't confirm, but could exist, in using wp_update_attachment_metadata
*  Code cleanup, formatting, standards

= 0.3 =
*  An option to assign the posts created through Automatic Featured Image Posts to any of your existing custom post types has been added.
*  General code cleanup & refactoring.
*  Language file update to match new settings.

= 0.2 =
*  Images uploaded through the 'edit post' screen are now assigned the categories of that post. Requested by jackthalad. Will be a configurable option in next release.

= 0.1 =
*  In which a plugin begins its life.


== Upgrade Notice ==

= 1.0 =
* New hooks. Compatible with 3.7.

= 0.9 =
* New hooks. Now skips images that are uploaded to existing posts.

= 0.8 =
* Bug fix for new post's date.

= 0.7 =
* Bug fixes when saving options while post formats are not enabled

= 0.6 =
*  NEW - Filters added for post title, categories, and content.

= 0.5 =
*  NEW - Option to assign automatically created posts to registered post formats.

= 0.4 =
*  Upgrade not required, but things are handled a little differently behind the scenes.

= 0.3 =
*  NEW - Option to assign posts created through Automatic Featured Image Posts to any of your existing custom post types.

= 0.2 =
*  Posts created by images uploaded through 'edit post' inherit the category of that existing post.

= 0.1 =
*  Initial installation.