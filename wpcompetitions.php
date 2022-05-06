<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Competitions
 * Plugin URI:        https://digital-drive.com/
 * Description:       WooCommerce extension for competition product type. Enables lotteries / competitions.
 * Version:           1.0.0
 * Requires at least: 5.7.0
 * Requires PHP:      7.4
 * Author:            Digital Drive
 * Author URI:        https://digital-drive.com/
 * Text Domain:       plugin-slug
 * License:           -
 * License URI:       -
 * Update URI:        https://digital-drive.com/
 */
defined('ABSPATH') || exit;
//Core defines for plugin operation
define('WPDIGITALDRIVE_COMPETITIONS_VERSION', '0.01');
define('WPDIGITALDRIVE_COMPETITIONS_FOLDER', dirname(plugin_basename(__FILE__)));
define('WPDIGITALDRIVE_COMPETITIONS_PATH', plugin_dir_path(__FILE__));
define('WPDIGITALDRIVE_COMPETITIONS_FILE', __FILE__);
define('WPDIGITALDRIVE_COMPETITIONS_URL', plugins_url('', __FILE__) . '/');
define('WPDIGITALDRIVE_COMPETITIONS_AJAX_PREFIX', 'wpplugin_'); //Used to create the wp ajax endpoints
define('WPDIGITALDRIVE_COMPETITIONS_NAMESPACE', 'WpDigitalDriveCompetitions'); //Used to create the wp ajax endpoints
define('WPDIGITALDRIVE_COMPETITIONS_SITEURL', site_url() ); //Used to create the wp ajax endpoints

// Composer autoloader
require 'vendor/autoload.php';

// Activation Hooks
\WpDigitalDriveCompetitions\Hooks\Loader::init();

// Admin pages
\WpDigitalDriveCompetitions\AdminPages\Loader::init();

// Ajax
\WpDigitalDriveCompetitions\Ajax\Loader::init();

// Cron
// \WpDigitalDriveCompetitions\Cron\Loader::init();

// Shortcode
\WpDigitalDriveCompetitions\Shortcode\Loader::init();
