<?php

/**
 * Plugin Name: Band Gallery Widget Addon For Elementor
 * Description: The Band Gallery Widget addon for Elementor will display images with different aspect ratios with the same height fulfilling the whole page or container width.
 * Plugin URI:  https://internet-mir.ru/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version:     1.0.0
 * Author:      Sergey Beloglazov
 * Author URI:  https://internet-mir.ru/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * License:     GNU General Public License v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: bgae
 * Elementor tested up to: 3.6.7
 * 
 * The Band Gallery Widget Addon For Elementor is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * The Band Gallery Widget Addon For Elementor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */



if (!defined('ABSPATH')) {
    exit;
}

if (defined('BGAE_VERSION')) {
    return;
}

define('BGAE_VERSION', '1.0.0');
define('BGAE_FILE', __FILE__);
define('BGAE_PATH', plugin_dir_path(BGAE_FILE));
define('BGAE_URL', plugin_dir_url(BGAE_FILE));

register_activation_hook(BGAE_FILE, array('BandGallery_Widget_Addon', 'bgae_activate'));
register_deactivation_hook(BGAE_FILE, array('BandGallery_Widget_Addon', 'bgae_deactivate'));

/**
 * Class BandGallery_Widget_Addon
 */
final class BandGallery_Widget_Addon
{

    /**
     * Plugin instance.
     *
     * @var BandGallery_Widget_Addon
     * @access private
     */
    private static $instance = null;

    /**
     * Get plugin instance.
     *
     * @return BandGallery_Widget_Addon
     * @static
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @access private
     */
    private function __construct()
    {
        //Load the plugin after Elementor (and other plugins) are loaded. 
        add_action('plugins_loaded', array($this, 'bgae_plugins_loaded'));
    }


    /**
     * Code you want to run when all other plugins loaded.
     */
    function bgae_plugins_loaded()
    {

        // Notice if the Elementor is not active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'bgae_fail_to_load'));
            return;
        }

        load_plugin_textdomain('bgae', false, BGAE_FILE . 'languages');


        // Require the main plugin file
        require(__DIR__ . '/includes/class-bgae.php');
    }


    function bgae_fail_to_load()
    {

        //Check if Elementor is active
        if (!is_plugin_active('elementor/elementor.php')) {
?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <?php echo sprintf(
                        __('<a href="%s"  target="_blank" >Elementor Page Builder</a>'
                            . '  must be installed and activated for "<strong>Band Gallery Widget Addon For Elementor</strong>" to work'),
                        'https://wordpress.org/plugins/elementor/'
                    ); ?></p>
            </div>
<?php
        }// /Check if Elementor is active
    }

    /**
     * Run when activate plugin.
     */
    public static function bgae_activate()
    {
        update_option("bgae-v", BGAE_VERSION);
        update_option("bgae-installDate", date('Y-m-d h:i:s'));
    }

    /**
     * Run when deactivate plugin.
     */
    public static function bgae_deactivate()
    {
    }
}

function BandGallery_Widget_Addon()
{
    return BandGallery_Widget_Addon::get_instance();
}

$GLOBALS['BandGallery_Widget_Addon'] = BandGallery_Widget_Addon();
