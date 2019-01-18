<?php

/**
* @package ZephyrProjectManager
*
* Plugin Name: 	Zephyr Project Manager
* Description: 	A modern project manager for WordPress to keep track of all your projects from within WordPress.
* Plugin URI: 	https://zephyr-one.com
* Version: 		2.79.0
* Author: 		Dylan J. Kotze
* License: 		GPLv2 or later
* Text Domain: 	zephyr_project_manager
*/

if ( !defined( 'ABSPATH' ) ) {
	die;
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

global $wpdb;

use Inc\Base\Activate;
use Inc\Base\Deactivate;

define( 'ZPM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ZPM_PLUGIN', plugin_basename( __FILE__ ) );
define( 'ZPM_PROJECTS_TABLE', $wpdb->prefix . 'zpm_projects' );
define( 'ZPM_TASKS_TABLE', $wpdb->prefix . 'zpm_tasks' );
define( 'ZPM_MESSAGES_TABLE', $wpdb->prefix . 'zpm_messages' );
define( 'ZPM_CATEGORY_TABLE', $wpdb->prefix . 'zpm_categories' );
define( 'ZPM_ACTIVITY_TABLE', $wpdb->prefix . 'zpm_activity' );
define( 'ZEPHYR_PRO_LINK', 'https://zephyr-one.com/purchase-pro/' );

function activate_project_manager_plugin() {
	Activate::activate();
}
register_activation_hook( __FILE__, 'activate_project_manager_plugin' );

function deactivate_project_manager_plugin() {
	Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_project_manager_plugin' );

if ( class_exists( 'Inc\\Init' ) ) {
	Inc\Init::register_services();
}