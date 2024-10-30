<?php

/**
 * The plugin bootstrap file
 *
 * This file also includes all of the dependencies used by the plugin, and defines a function
 * that starts the plugin.
 *
 * @link    http://squarestarworkshop.com/wordpress-plugins/media-defaults/
 * @since   1.0.0
 * @package MediaDefaults
 *
 * @wordpress-plugin
 * Plugin Name: Media Defaults
 * Plugin URI:  http://squarestarworkshop.com/wordpress-plugins/media-defaults/
 * Description: Set site-wide defaults for creating new galleries (number of columns, link destination, size, etc.) and the attachment filter for inserting new media ("Uploaded to this post" vs. "Images").
 * Version:     1.1.0
 * Author:      Square Star Workshop
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: sswmd
 * Domain Path: /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-media-defaults-activator.php
 */
function activateMediaDefaults()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-media-defaults-activator.php';
    MediaDefaultsActivator::activate();
}

register_activation_hook(__FILE__, 'activateMediaDefaults');

/**
 * The core plugin class that is used to define internationalization and dashboard-specific hooks
 */
require plugin_dir_path(__FILE__) . 'includes/class-media-defaults.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin 
 * from this point in the file does not affect the page life cycle.
 *
 * @since   1.0.0
 */
function runMediaDefaults()
{
    $plugin = new MediaDefaults();
    $plugin->run();
}

runMediaDefaults();
