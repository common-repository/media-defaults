<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://squarestareworkshop.com
 * @since      1.0.0
 *
 * @package    MediaDefaults
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('media_defaults_inserting');
delete_option('media_defaults_galleries');
delete_option('media_defaults_plugin_version');
delete_transient('media_defaults_activated');

