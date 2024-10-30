<?php

/**
 * The file that defines the core plugin class
 *
 * Attributes and functions used across the plugin. Can be extended to incorporate any
 * public-facing features of the plugin
 *
 * @link        http://squarestareworkshop.com
 * @since       1.0.0
 * 
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization and dashboard-specific hooks
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since       1.0.0
 * @package     MediaDefaults
 * @subpackage  MediaDefaults/includes
 * @author      Eoin Flood <info@squarestarmedia.ie>
 */
class MediaDefaults
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since   1.0.0
     * @access  protected
     * 
     * @var MediaDefaultsLoader   $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since   1.0.0
     * @access  protected
     * 
     * @var string  $pluginName The string used to uniquely identify this plugin.
     */
    protected $pluginName;

    /**
     * The current version of the plugin.
     *
     * @since   1.0.0
     * @access  protected
     * 
     * @var string  $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale and set the hooks for the Dashboard
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        $this->pluginName = 'media-defaults';
        $this->version = '1.1.1';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MediaDefaultsLoader. Orchestrates the hooks of the plugin.
     * - MediaDefaultsI18n. Defines internationalization functionality.
     * - MediaDefaultsAdmin. Defines all hooks for the dashboard.
     * - MediaDefaultsPublic. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since   1.0.0
     * @access  private
     */
    private function loadDependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) 
            . 'includes/class-media-defaults-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) 
            . 'includes/class-media-defaults-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) 
            . 'admin/class-media-defaults-admin.php';

        $this->loader = new MediaDefaultsLoader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MediaDefaults_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since   1.0.0
     * @access	private
     */
    private function setLocale()
    {
        $pluginI18n = new MediaDefaultsI18n();
        $pluginI18n->setDomain('sswmd');

        $this->loader->addAction('plugins_loaded', $pluginI18n, 'loadPluginTextdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality of the plugin.
     *
     * @since	1.0.0
     * @access	private
     */
    private function defineAdminHooks()
    {
        $pluginAdmin = new MediaDefaultsAdmin($this->getPluginName(), $this->getVersion());

        $this->loader->addAction(
            'admin_notices', $pluginAdmin, 'pluginActivatedNotice', 11
        );

        $this->loader->addAction(
            'admin_init', $pluginAdmin, 'adddMediaSettings'
        );
        
        $this->loader->addAction(
            'admin_enqueue_scripts', $pluginAdmin, 'enqueueScripts'
        );

        $this->loader->addAction(
            'admin_footer', $pluginAdmin, 'printFooterScript'
        );
        
        $this->loader->addAction(
            'admin_footer', $pluginAdmin, 'printMediaTemplates'
        );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since   1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since	1.0.0
     * 
     * @return  string  The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since	1.0.0
     * 
     * @return  MediaDefaults_Loader   Orchestrates the hooks of the plugin.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since	1.0.0
     * 
     * @return  string  The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }

}
