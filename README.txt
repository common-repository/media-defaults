=== Media Defaults ===
Contributors: squarestar
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=GQUFH6EBXXWQU&lc=IE&item_name=Square%20Star%20Workshop&item_number=wp%2dmedia%2ddefaults&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: media, settings, gallery, galleries, image, images, audio, video, videos, playlist, playlists, admin
Requires at least: 3.6
Tested up to: 4.6
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set site-wide defaults for creating galleries and inserting media.

== Description ==

*Media Defaults* allows you to specify default settings for inserting media and galleries into posts,
pages and custom post types. The following list shows all the items that can have a custom default 
setting:

* Inserting Media
 * Attachment Filter - display only items "Uploaded to this post" or any other filter, such as 
"Audio" or "Unattached"
 * Alignment - the default alignment setting for images
 * Attachment link - what happens when the user clicks on the media item, e.g., "Embed Media Player", 
"Link to Attachment Page", "None", etc.
 * Attachment Size - the default size setting for images 
* Adding Galleries
 * Thumbnail link - the destination when users click on an image in the gallery
 * Number of columns - how many images to display in each gallery row
 * Random order - whether *Random* will be selected by default for new galleries
 * Thumbnail size - the default size when creating new galleries

*Media Defaults* does this in a way that is unobtrusive and integrates seamlessly with WordPress' 
native interface. See the screenshots for examples.

*Media Defaults* uses WordPress' native defaults when installed so you won't notice any change
until you alter the settings to your liking.

*Media Defaults* does **NOT** make changes to existing content, galleries or to WordPress' [gallery] 
shortcode. It only adds new sections to *Settings > Media* in the WordPress admin area that allow 
you to specify new defaults for creating galleries or inserting media, and implements those 
defaults in the "Add Media" popup screen.

Requires WordPress 3.6 and PHP 5.

== Installation ==

1. Upload the `media-defaults` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit *Settings > Media* in the WordPress admin area to modify the new settings

== Frequently Asked Questions ==

= Will Media Defaults affect my existing posts, galleries or media? =

No. *Media Defaults* only modifies the defaults to which the various checkboxes and dropdowns are set
when you go to add a new gallery or media item to a post, page or custom post-type. It does not 
modify any output to the front-end of your website and does not make any changes to any existing 
content on your site.

= Does Media Defaults conflict with any other plugins or themes? =

*Media Defaults* has no known conflicts at time of writing. If you find a suspected conflict with 
a plugin or theme and want to report it, please use the *Support* section of this plugin on 
wordpress.org and include:

* The name, URL and version number of the plugin or theme you think is causing a problem
* Any on-screen errors displayed that are relevant to the issue
* The negative effect it is having on your site

= What happens if I choose "Audio" or other specific media type as the default attachment filter? =

If, for example, you choose "Audio" as the default attachment filter, the plugin intelligently
recognises when you are trying to do something that cannot be done with audio media and will change
to that type. So if you have "Audio" as your default and you select "Create Video Playlist", the
view will display only video files.

= Can you provide Media Defaults in my language? =

*Media Defaults* is translation-ready. I do not have the necessary skills to translate the plugin 
myself. If you would like to provide a translation of the plugin, I will include it in future 
versions and give you credit for the work.

= What happens if I uninstall Media Defaults? =

The only difference you will notice is that the default settings for media items will be different
the next time you go to insert a media item or gallery into a post (assuming you set your own
preferences while the plugin was installed). 

Behind the scenes, *Media Defaults* adds 4 entries to the `wp_options` table to save your new 
defaults. When you uninstall the plugin, these entries will be removed. These entries will 
**not** be removed if you only **deactivate** the plugin.

= Can you add such-and-such a feature? =

Possibly :-) - Submit a suggestion or request on the wordpress.org *Support* section for the plugin 
and I will see if it can be done.

== Screenshots ==

1. The plugin admin interface
2. Inserting images showing new default settings selected
3. Inserting a gallery showing new defaults loaded

== Changelog ==

= 1.1.1 [2016 November 07] =
* Bugfix: When editing an existing image gallery no images would be displayed

= 1.1.0 [2016 October 21] =
* Added more options to Settings > Media under "Inserting Media"
* Changed how the previous "Uploaded to this post" default is set. The plugin will automatically
recognise the previous setting and update the new version to match

= 1.0.1 [2016 October 12] =
* Fixed issue with "Uploaded to this post" always being selected when adding media

= 1.0.0 =
* Initial release
