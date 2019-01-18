<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Base\BaseController;

class Zephyr {

	public function __construct() {

	}

	public static function isPro() {
		if (class_exists('Inc\\ZephyrProjectManager\\Plugin')) {
			return true;
		}

		return false;
	}

	public static function getPluginData() {
		$plugin_data = get_plugin_data( ZPM_PLUGIN_PATH . '/zephyr-project-manager.php' );
		return $plugin_data;
	}

	// Returns the data for the Pro Add On
	public static function getProPluginData() {

		if (!Zephyr::isPro()) {
			return false;
		}

		$plugin_data = get_plugin_data( ZEPHYR_PRO_PLUGIN_PATH . '/zephyr-project-manager-pro.php' );
		return $plugin_data;
	}

	// Returns the version of the basic plugin
	public static function getPluginVersion() {
		$plugin_data = Zephyr::getPluginData();
		return $plugin_data['Version'];
	}

	// Returns the version of the pro add on
	public static function getProPluginVersion() {

		if (!Zephyr::isPro()) {
			return false;
		}

		$plugin_data = Zephyr::getProPluginData();
		return $plugin_data['Version'];
	}

}