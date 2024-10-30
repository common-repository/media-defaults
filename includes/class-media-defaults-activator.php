<?php

/**
 * Fired during plugin activation
 *
 * @link        http://squarestarworkshop.ocm
 * @since       1.0.0
 * 
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since       1.0.0
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/includes
 * @author      Eoin Flood <info@squarestarmedia.ie>
 */
class MediaDefaultsActivator
{

    /**
     * The main function that fires during plugin activation
     *
     * Set a transient to be used by another hook to display an admin notice on activation
     *
     * @since   1.0.0
     */
    public static function activate()
    {
        set_transient('media_defaults_activated', true, 10);
    }

}
