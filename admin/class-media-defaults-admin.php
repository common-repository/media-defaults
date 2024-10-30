<?php

/**
 * The main functionality of the plugin.
 *
 * @link        http://squarestareworkshop.com
 * @since       1.0.0
 * 
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and all hooks required for the plugin to run
 *
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/admin
 * @author      Eoin Flood <info@squarestarmedia.ie>
 */
class MediaDefaultsAdmin
{
    /**
     * The ID of this plugin.
     *
     * @since   1.0.0
     * @access  private
     * 
     * @var string  $pluginName The ID of this plugin.
     */
    private $pluginName;

    /**
     * The version of this plugin.
     *
     * @since   1.0.0
     * @access  private
     * 
     * @var string  $version    The current version of this plugin.
     */
    private $version;
    
    /**
     * Array of all plugin options 
     *
     * @since   1.1.0
     * @access  private
     * 
     * @var array  $currentOptions    Array of options
     */
    private $currentOptions;

    /**
     * Initialize the class, set its properties and perform any update actions required.
     *
     * @since   1.0.0
     * 
     * @param   string  $pluginName The name of this plugin.
     * @param   string  $version    The version of this plugin.
     */
    public function __construct($pluginName, $version)
    {
        $this->pluginName = $pluginName;
        $this->version = $version;
        
        $this->currentOptions['inserting'] = $this->currentOptions('inserting');
        $this->currentOptions['galleries'] = $this->currentOptions('galleries');
        
        $this->maybeUpdatePlugin();
    }
    
    //==============================================================================================
    // Plugin Meta
    //----------------------------------------------------------------------------------------------
    
    /**
     * Returns an associative array of options with default values
     * 
     * @since   1.1.0
     * 
     * @return array 
     */
    private static function defaultOptions($section = false)
    {
        $defaultOptions = array(
            'inserting' => array(
                'attachment_filter'                     => 'all',
                'attachment_display_alignment'          => 'none',
                'attachment_display_link_to'            => 'none',
                'attachment_display_link_to_embeddable' => 'embed',
                'attachment_display_size'               => 'full',
            ),
            'galleries' => array(
                'link_to'        => 'post',
                'column_count'   => 3,
                'toggle_random'  => 0,
                'thumbnail_size' => 'thumbnail',
                'hide_donate'    => 0,
            ),
        );
        
        if (is_string($section)) {
            if (isset($defaultOptions[$section])) {
                return $defaultOptions[$section];
            } else {
                throw new \Exception('Invalid section-name');
            }
        } else {
            return $defaultOptions;
        }
    }
    
    /**
     * Retrieves a sub-array of options from an array of current options and their values.
     * 
     * @since   1.1.0
     * 
     * @param string $section The key of the sub-array to retrieve
     * 
     * @return array The sub-array, if it exists. Otherwise an empty array.
     */
    private function currentOptions($section)
    {
        $defaultOptions = self::defaultOptions();
        
        $savedOptions = array(
            'inserting' => get_option('media_defaults_inserting', array()),
            'galleries' => get_option('media_defaults_galleries', array()),
        );
        
        if (isset($defaultOptions[$section])) {
            if (isset($savedOptions[$section])) {
                return array_merge($defaultOptions[$section], $savedOptions[$section]);
            }
            
            return $defaultOptions[$section];
        }
        
        return array();
    }
    
    /**
     * Fires any code required after checking the installed version of the plugin against
     * the current version.
     * 
     * Compares the installed version number, saved in the database, against the version 
     * number of the plugin and executes any required code. Also updates the database 
     * installed version number when complete.
     * 
     * Future versions of the plugin may require a different approach
     * 
     * @since   1.1.0
     * 
     * @return void
     */
    private function maybeUpdatePlugin()
    {
        $installedVersion = get_option('media_defaults_plugin_version');
        $versionCompare = -1;
        if (!empty($installedVersion)) {
            $versionCompare = version_compare($installedVersion, $this->version);
        }
        
        // No need to update if same or newer
        if ($versionCompare !== -1) {
            return;
        }
        
        /**
         * Check for older version of the attachment filter setting and set the new version
         * to match
         * 
         * @since 1.1.0
         */
        if (isset($this->currentOptions['inserting']['uploaded_to']) 
            && $this->currentOptions['inserting']['uploaded_to']
        ) {
            $this->currentOptions['inserting']['attachment_filter'] = 'uploaded';
        }
        
        update_option('media_defaults_plugin_version', $this->version);
    }
    
    /**
     * Prints an admin notice when the plugin is activated
     * 
     * @since   1.1.0
     */
    public function pluginActivatedNotice()
    {
        if (get_transient('media_defaults_activated')) : ?>

<div class="updated notice is-dismissible">
    <p><?php _e('Thank you for using Media Defaults! You can now set your new defaults here', 'sswmd'); 
        ?>: <a href="<?php echo admin_url('options-media.php'); ?>"><em><?php 
        _e('Settings &gt; Media', 'sswmd'); ?></em></a>.
    </p>
</div>

        <?php 
        delete_transient('media_defaults_activated');
        endif;
    }
    
    //==============================================================================================
    // Settings
    //----------------------------------------------------------------------------------------------
    
    /**
     * Add new settings sections and settings to the Settings > Media page
     * 
     * @since   1.0.0
     */
    public function adddMediaSettings()
    {
        // Inserting
        //------------------------------------------------------------------------------------------
        
        add_settings_section(
            'media_defaults_inserting', __('Inserting Media', 'sswmd'), '__return_false',  'media'
        );
        
        add_settings_field(
            'attachment_filter', __('Default attachment filter', 'sswmd'), 
            array($this, 'insertingAttachmentFilter'), 'media', 'media_defaults_inserting',
            array('label_for' => 'inserting_attachment_filter')
        );
        
        add_settings_field(
            'attachment_display_alignment', __('Default attachment alignment', 'sswmd'), 
            array($this, 'insertingAttachmentAlignment'), 'media', 'media_defaults_inserting',
            array('label_for' => 'inserting_attachment_alignment')
        );
        
        add_settings_field(
            'attachment_display_link_to', __('Default attachment link destination', 'sswmd'), 
            array($this, 'insertingAttachmentLinkTo'), 'media', 'media_defaults_inserting',
            array('label_for' => 'inserting_attachment_link_to')
        );
        
        add_settings_field(
            'attachment_display_link_to_embeddable', 
            __('Default embeddable attachment link destination', 'sswmd'), 
            array($this, 'insertingAttachmentLinkToEmbedable'), 'media', 'media_defaults_inserting',
            array('label_for' => 'inserting_attachment_link_to_embeddable')
        );
        
        add_settings_field(
            'attachment_display_size', __('Default attachment size', 'sswmd'), 
            array($this, 'insertingAttachmentSize'), 'media', 'media_defaults_inserting',
            array('label_for' => 'inserting_attachment_size')
        );

        register_setting(
            'media', 'media_defaults_inserting', array($this, 'sanitizeInsertingSettings')
        );
        
        // Galleries
        //------------------------------------------------------------------------------------------
        
        add_settings_section(
            'media_defaults_galleries', __('Adding Galleries', 'sswmd'), '__return_false',  'media'
        );
        
        add_settings_field(
            'link_to', __('Default image link', 'sswmd'), array($this, 'galleriesLinkTo'),
            'media', 'media_defaults_galleries', array('label_for' => 'galleries_link_to')
        );
        
        add_settings_field(
            'column_count', __('Default number of columns', 'sswmd'), 
            array($this, 'galleriesColumnCount'), 'media', 'media_defaults_galleries',
            array('label_for' => 'galleries_column_count')
        );
        
        add_settings_field(
            'toggle_random', __('Random', 'sswmd'), 
            array($this, 'galleriesToggleRandom'), 'media', 'media_defaults_galleries', 
            array('label_for' => 'galleries_toggle_random')
        );
        
        add_settings_field(
            'thumbnail_size', __('Default image size', 'sswmd'),
            array($this, 'galleriesThumbnailSize'), 'media', 'media_defaults_galleries',
            array('label_for' => 'galleries_thumbnail_size')
        );
        
        add_settings_field(
            'hide_donate', __('Donate to <em>Media Defaults</em>', 'sswmd'),
            array($this, 'printDonate'), 'media', 'media_defaults_galleries'
        );

        register_setting('media', 'media_defaults_galleries', array($this, 'sanitizeGalleriesSettings'));
    }

    // Settings - Processing
    //----------------------------------------------------------------------------------------------
    
    /**
     * Sanitize the "Inserting" section settings
     * 
     * @since   1.0.0
     * 
     * @param array $settings
     * 
     * @return array
     */
    public function sanitizeInsertingSettings(array $settings)
    {
        $settings['attachment_filter'] = $this->sanitizeSelectOption(
            $settings, 'attachment_filter', 
            array('all', 'uploaded', 'image', 'audio', 'video', 'unattached'), 'inserting'
        );
        
        $settings['attachment_display_alignment'] = $this->sanitizeSelectOption(
            $settings, 'attachment_display_alignment', array('left', 'center', 'right', 'none'), 
            'inserting'
        );
        
        $settings['attachment_display_link_to'] = $this->sanitizeSelectOption(
            $settings, 'attachment_display_link_to', array('post', 'file', 'none', 'custom'), 
            'inserting'
        );
        
        $settings['attachment_display_link_to_embeddable'] = $this->sanitizeSelectOption(
            $settings, 'attachment_display_link_to_embeddable', array('post', 'file', 'embed'), 
            'inserting'
        );
        
        $settings['attachment_display_size'] = $this->sanitizeSelectOption(
            $settings, 'attachment_display_size', array('thumbnail', 'medium', 'large', 'full'), 
            'inserting'
        );
        
        return $settings;
    }
    
    /**
     * Sanitize the "Galleries" section settings
     * 
     * @since   1.0.0
     * 
     * @param array $settings
     * 
     * @return array
     */
    public function sanitizeGalleriesSettings(array $settings)
    {
        // Link
        $settings['link_to'] = $this->sanitizeSelectOption(
            $settings, 'link_to', array('post', 'file', 'none'), 
            'galleries'
        );
        
        // Number of columns
        if (isset($settings['column_count'])) {
            $settings['column_count'] = is_numeric($settings['column_count']) 
                && ($settings['column_count'] >= 1 && $settings['column_count'] <= 10)
                    ? $settings['column_count'] : 3;
        }
        
        // Random
        $settings['toggle_random'] = $this->sanitizeCheckbox(
            $settings, 'toggle_random', 'galleries'
        );
        
        // Thumbnail size
        $settings['thumbnail_size'] = $this->sanitizeSelectOption(
            $settings, 'thumbnail_size', array('thumbnail', 'medium', 'large', 'full'), 
            'galleries'
        );
        
        // Donate button
        $settings['hide_donate'] = $this->sanitizeCheckbox(
            $settings, 'hide_donate', 'galleries'
        );

        return $settings;
    }
    
    /**
     * Checks the value of a checkbox item and returns either the validated value or its default
     * 
     * @since   1.1.0
     * 
     * @param array  $settings The array of settings in which the setting being checked is found
     * @param string $name     The name of the setting to check 
     * @param string $section  The options section. Must be one of 'inserting' or 'galleries'
     * 
     * @return mixed The valid value or, if not valid, the default if the setting name is valid
     */
    private function sanitizeCheckbox(array $settings, $name, $section)
    {
        $validation = $settings[$name] == 1;
        
        return $this->sanitizeItem($settings, $name, $section, $validation);
    }
    
    /**
     * Checks a value against an array of valid values for use as an <option> in a <select> and
     * returns the validated value or its default
     * 
     * @since   1.1.0
     * 
     * @param array  $settings The array of settings in which the setting being checked is found
     * @param string $name     The name of the setting to check 
     * @param array  $options  The array of valid values for the setting
     * @param string $section  The options section. Must be one of 'inserting' or 'galleries'
     * 
     * @return mixed The valid value or, if not valid, the default if the setting name is valid
     */
    private function sanitizeSelectOption(array $settings, $name, array $options, $section)
    {
        $validation = in_array($settings[$name], $options, true);
        
        return $this->sanitizeItem($settings, $name, $section, $validation);
    }
    
    /**
     * Processes a value and its validation and returns either the validated value or its default
     * 
     * @param array  $settings   The array of settings in which the setting being checked is found
     * @param string $name       The name of the setting to check
     * @param string $section    The options section. Must be 'inserting' or 'galleries'
     * @param bool   $validation The validation used to determine what value to return
     * 
     * @return mixed
     */
    private function sanitizeItem(array $settings, $name, $section, $validation)
    {
        // Get the defaults for the supplied section
        $defaults = self::defaultOptions($section);
        // No setting should be checked if it has no default value
        if (!isset($defaults[$name])) {
            throw new \Exception('Invalid setting name');
        }
        
        // If the setting exists
        if (isset($settings[$name])) {
            // Return either the supplied value or the default depending on the supplied validation
            return $validation ? $settings[$name] : $defaults[$name];
        } 
        
        // Otherwise return the default
        return $defaults[$name];
    }
    
    // Settings - Output Functions
    //----------------------------------------------------------------------------------------------
    // Inserting

    /**
     * Callback to display the "Default attachment filter" field
     * 
     * @since   1.1.0
     */
    public function insertingAttachmentFilter()
    {
        $selected = $this->currentOptions['inserting']['attachment_filter'];
        ?>

<label>
    <select id="inserting_attachment_filter" name="media_defaults_inserting[attachment_filter]">
    
    <?php            
    $filters = array(
        'all'        => __('All media items', 'sswmd'),
        'uploaded'   => __('Uploaded to this post', 'sswmd'),
        'image'      => __('Images', 'sswmd'),
        'audio'      => __('Audio', 'sswmd'),
        'video'      => __('Video', 'sswmd'),
        'unattached' => __('Unattached', 'sswmd'),
    );
    foreach ($filters as $filter => $label) : ?>
        
        <option value="<?php echo esc_attr($filter); ?>" <?php 
            selected(esc_attr($filter), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>
        
    <?php endforeach; ?>

    </select>
</label>

        <?php
    }

    /**
     * Callback to display the "Default attachment alignment" field
     * 
     * @since   1.1.0
     */
    public function insertingAttachmentAlignment()
    {
        $selected = $this->currentOptions['inserting']['attachment_display_alignment'];
        ?>

<label>
    <select id="inserting_attachment_display_alignment" name="media_defaults_inserting[attachment_display_alignment]">
    
    <?php            
    $alignments = array(
        'left'   => __('Left', 'sswmd'),
        'center'   => __('Center', 'sswmd'),
        'right'   => __('Right', 'sswmd'),
        'none' => __('None', 'sswmd'),
    );
    foreach ($alignments as $alignment => $label) : ?>
        
        <option value="<?php echo esc_attr($alignment); ?>" <?php 
            selected(esc_attr($alignment), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>
        
    <?php endforeach; ?>

    </select>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Default attachment link destination" field
     * 
     * @since   1.1.0
     */
    public function insertingAttachmentLinkTo()
    {
        $selected = $this->currentOptions['inserting']['attachment_display_link_to'];
        ?>

<label>
    <select id="inserting_attachment_display_link_to" name="media_defaults_inserting[attachment_display_link_to]">
    <?php            
    $linkDestinations = array(
        'none'   => __('None', 'sswmd'),
        'file'   => __('Media File', 'sswmd'),
        'post'   => __('Attachment Page', 'sswmd'),
        'custom' => __('Custom URL', 'sswmd'),
    );
    foreach ($linkDestinations as $destination => $label) : ?>
        
        <option value="<?php echo esc_attr($destination); ?>" <?php 
            selected(esc_attr($destination), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>
        
    <?php endforeach; ?>
    </select>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Default embeddable attachment link destination" field
     * 
     * @since   1.1.0
     */
    public function insertingAttachmentLinkToEmbedable()
    {
        $selected = $this->currentOptions['inserting']['attachment_display_link_to_embeddable'];
        ?>

<label>
    <select id="inserting_attachment_display_link_to_embeddable" name="media_defaults_inserting[attachment_display_link_to_embeddable]">
        
    <?php            
    $embedDestinations = array(
        'embed' => __('Embed Media Player', 'sswmd'),
        'file' => __('Link to Media File', 'sswmd'),
        'post' => __('Link to Attachment Page', 'sswmd'),
    );
    foreach ($embedDestinations as $destination => $label) : ?>
        
        <option value="<?php echo esc_attr($destination); ?>" <?php 
            selected(esc_attr($destination), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>
        
    <?php endforeach; ?>
        
    </select>
</label>

<p class="description">
    <?php _e('For media such as MP3s or videos that can be embedded', 'sswmd'); ?>
</p>

        <?php
    }
    
    /**
     * Callback to display the "Default attachment size" field
     * 
     * @since   1.1.0
     */
    public function insertingAttachmentSize()
    {
        $selected = $this->currentOptions['inserting']['attachment_display_size'];
        ?>

<label>
    <select id="inserting_attachment_display_size" name="media_defaults_inserting[attachment_display_size]">
    
    <?php            
    $sizeNames = apply_filters( 'image_size_names_choose', array(
        'thumbnail' => __('Thumbnail', 'sswmd'),
        'medium'    => __('Medium', 'sswmd'),
        'large'     => __('Large', 'sswmd'),
        'full'      => __('Full Size', 'sswmd'),
    ));
    foreach ($sizeNames as $size => $label) : ?>
        <option value="<?php echo esc_attr($size); ?>" <?php selected(esc_attr($size), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>

    <?php endforeach; ?>

    </select>
</label>

        <?php
    }
    
    // Galleries
    
    /**
     * Callback to display the "Default image link" field
     * 
     * @since   1.0.0
     */
    public function galleriesLinkTo()
    {
        $selected = $this->currentOptions['galleries']['link_to'];
        ?>

<label>
    <select id="galleries_link_to" name="media_defaults_galleries[link_to]">
    
    <?php            
    $destinations = array(
        'post'   => __('Attachment Page', 'sswmd'),
        'file'   => __('Media File', 'sswmd'),
        'none'   => __('None', 'sswmd'),
    );
    foreach ($destinations as $destination => $label) : ?>
        
        <option value="<?php echo esc_attr($destination); ?>" <?php 
            selected(esc_attr($destination), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>
        
    <?php endforeach; ?>

    </select>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Default column-count" field
     * 
     * @since   1.0.0
     */
    public function galleriesColumnCount()
    {
        $selected = $this->currentOptions['galleries']['column_count'];
        ?>

<label>
    <select id="galleries_column_count" name="media_defaults_galleries[column_count]">
        <?php for ($i = 1; $i <= 10; $i++) : ?>
            <option value="<?php echo $i; ?>" <?php selected($i, $selected); ?>>
                <?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Random" field
     * 
     * @since   1.0.0
     */
    public function galleriesToggleRandom()
    {
        $checked = $this->currentOptions['galleries']['toggle_random'];
        ?>

<label>
    <input type="checkbox" id="galleries_toggle_random" <?php checked(1, $checked); ?> 
           name="media_defaults_galleries[toggle_random]" value="1">
    <?php _e('Sort galleries randomly by default', 'sswmd'); ?>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Default thumnail size" field
     * 
     * @since   1.0.0
     */
    public function galleriesThumbnailSize()
    {
        $selected = $this->currentOptions['galleries']['thumbnail_size'];
        ?>

<label>
    <select id="galleries_thumbnail_size" name="media_defaults_galleries[thumbnail_size]">
    
    <?php            
    $sizeNames = apply_filters( 'image_size_names_choose', array(
        'thumbnail' => __('Thumbnail', 'sswmd'),
        'medium'    => __('Medium', 'sswmd'),
        'large'     => __('Large', 'sswmd'),
        'full'      => __('Full Size', 'sswmd'),
    ));
    foreach ($sizeNames as $size => $label) : ?>
        <option value="<?php echo esc_attr($size); ?>" <?php selected(esc_attr($size), $selected); ?>>
            <?php echo esc_html($label); ?>
        </option>

    <?php endforeach; ?>

    </select>
</label>

        <?php
    }
    
    /**
     * Callback to display the "Donate to Media Defaults" field
     * 
     * @since   1.0.0
     */
    public function printDonate()
    {
        $checked = $this->currentOptions['galleries']['hide_donate'];        
        if (!$checked) :
        ?>

<div class="media-defaults-donate">
    <p><?php _e(
        '<strong><em>Inserting Media</em></strong> &amp; <strong><em>Adding Galleries</em></strong> options are enabled by the <em>Media Defaults</em> plugin', 'sswmd'
    ); ?>.</p>
    <p><?php _e(
        'If you would like to donate to the author of <em>Media Defaults</em>, please use the button below. Any amount is very welcome.', 'sswmd'
    ); ?></p>
    <style type="text/css">
        .paypal-button {
            display: inline-block;
            height: .8em;
            margin: .5em 0;
            padding: .4em 1em .5em;
            line-height: .8em;
            border-radius: 100px;
            border: 1px solid hsl(36, 99%, 79%);
            text-align: center;
            font-weight: 700;
            font-style: italic;
            text-decoration: none;
            text-shadow: 0 1px white;
            background-color: hsl(40, 98%, 84%);
            color: hsl(212, 37%, 33%);
            box-shadow: 0 -.5em .5em 0 hsl(36, 99%, 59%) inset, 
                0 0 .5em 0 hsl(0, 0%, 100%) inset;
        }
    </style>
    <p><a class="paypal-button" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=GQUFH6EBXXWQU&lc=IE&item_name=Square%20Star%20Workshop&item_number=wp%2dmedia%2ddefaults&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_new">
        Donate via PayPal
    </a></p>
</div><!-- /.media-defaults-donate -->

        <?php endif; ?>

<label>
    <input type="checkbox" id="hide_donate"  <?php checked(1, $checked); ?>
           name="media_defaults_galleries[hide_donate]" value="1"> 
    <?php _e('Hide the &quot;Donate&quot; button', 'sswmd'); ?>
</label>

        <?php 
    }

    //==============================================================================================
    // Main Output
    //----------------------------------------------------------------------------------------------

    /**
     * Enqueue the JavaScript for the admin area.
     *
     * @since   1.0.0
     */
    public function enqueueScripts()
    {
        if(wp_script_is('media-views', 'enqueued')) {
            wp_enqueue_script(
                $this->pluginName, plugin_dir_url(__FILE__) . 'js/media-defaults-admin.js', 
                array('jquery', 'media-editor'), $this->version, false
            );
            wp_localize_script($this->pluginName, 'sswMd', array(
                'inserting' => $this->currentOptions['inserting']
            ));
        }
    }
    
    /**
     * Print script to modify the wp.media.galleryDefaults object
     *
     * @since   1.1.0
     */
    public function printFooterScript()
    {
        // Do not print if media manager not included
        if(!wp_script_is('media-views', 'enqueued')) {
            return;
        }
        ?>

<script type="text/javascript">
    if (wp.media) {
        wp.media.galleryDefaults.link 
            = "<?php echo $this->currentOptions['galleries']['link_to']; ?>";
        wp.media.galleryDefaults.columns 
            = "<?php echo $this->currentOptions['galleries']['column_count']; ?>";
    }
</script>

        <?php
    }    
    
    /**
     * Print gallery template script with new defaults
     *
     * @since   1.1.0
     */
    public function printMediaTemplates()
    {
        // Do not print if media manager not included
        if(!wp_script_is('media-views', 'enqueued')) {
            return;
        }
        
        require_once plugin_dir_path(__FILE__) 
            . 'partials/media-defaults-admin-footer-templates.php';
    }
    
}
