<?php
/*
Plugin Name: Announcement Bar
Plugin URI: announcement-bar
Description: Display a banner at the top of your website.
Author: Smeedijzer Internet
Author URI: smeedijzer.net
Version: 1.0.0
License: MIT
Text Domain: announcement-bar
Domain Path: /languages
*/

if (! defined('ABSPATH')) {
    die('You are not allowed!');
}

//define consts
define('ANNOUNCEMENT_BAR_PLUGIN_BASE', dirname(plugin_basename(__FILE__)));
define('ANNOUNCEMENT_BAR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ANNOUNCEMENT_BAR_PLUGIN_URL', plugin_dir_url(__FILE__));


class Announcement_Bar
{
    public function __construct()
    {
        add_action('init', array( $this, 'load_text_domain' ));
        add_action('init', array( $this, 'init_admin' ));
        add_action('init', array( $this, 'init_frontend' ));
    }

    /**
     * Localization
     *
     * @return void
     */
	public static function load_text_domain(): void
    {
        load_plugin_textdomain('announcement-bar', false, ANNOUNCEMENT_BAR_PLUGIN_BASE. '/languages');
    }

    public function init_admin(): void
    {
        require_once(ANNOUNCEMENT_BAR_PLUGIN_PATH . 'includes/class-settings.php');
        new Announcement_Settings();
    }

    public function init_frontend(): void
    {
        require_once(ANNOUNCEMENT_BAR_PLUGIN_PATH . 'includes/class-announcement.php');
        new Announcement_Frontend();
    }
}

new Announcement_Bar();
