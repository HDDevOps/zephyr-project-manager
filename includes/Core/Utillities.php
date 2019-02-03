<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Base\BaseController;

class Utillities {
	public function __construct() {

	}

	/** 
	* Returns all of the users
	* @return object
	*/
	public static function get_users() {
		$users = get_users();
		$user_array = [];
		foreach ($users as $user) {
			$user = Utillities::get_user( $user->ID );
			array_push( $user_array, $user );
		}
		return $user_array;
	}

	/**
	* Gets the custom details of a project manager/project user. It returns the custom profile picture, name, bio, and email
	* @param int $id The ID of the user
	* @return array
	*/
	public static function get_user( $id ) {
		$current_user = get_user_by('ID', $id);
		$preferences = get_option( 'zpm_user_' . $id . '_settings' );
		$notify_activity = isset( $preferences['notify_activity'] ) ? $preferences['notify_activity'] : false;
		$notify_tasks = isset( $preferences['notify_tasks'] ) ? $preferences['notify_tasks'] : false;
		$notify_updates = isset( $preferences['notify_updates'] ) ? $preferences['notify_updates'] : false;

		$notification_preferences = [
			'notify_activity' => $notify_activity,
			'notify_tasks' 	  => $notify_updates,
			'notify_updates'  => $notify_updates
		];

		if ($id !== '-1' && is_object($current_user)) {
			$user_settings_option = get_option('zpm_user_' . $id . '_settings');
			$avatar = isset($user_settings_option['profile_picture']) ? $user_settings_option['profile_picture'] : get_avatar_url($id);
			$name = isset($user_settings_option['name']) ? $user_settings_option['name'] : $current_user->display_name;
			$description = isset($user_settings_option['description']) ? $user_settings_option['description'] : '';
			$email = isset($user_settings_option['email']) ? $user_settings_option['email'] : $current_user->user_email;
			$user_info = array(
				'id'		  => $id,
				'email' 	  => $email,
				'name' 		  => $name,
				'description' => $description,
				'avatar' 	  => $avatar,
				'preferences' => $notification_preferences
			);
			return $user_info;
		} else {
			return array(
				'id'		  => '',
				'email' 	  => '',
				'name' 		  => '',
				'description' => '',
				'avatar' 	  => '',
				'preferences' => $notification_preferences
			);
		}
	}


	/* Convert hexdec color string to rgb(a) string */
	public static function hex2rgba( $color, $opacity = false ) {
	 
		$default = 'rgb(0,0,0)';
	 
		//Return default if no color provided
		if(empty($color))
	          return $default; 
	 
			//Sanitize $color if "#" is provided 
	        if ($color[0] == '#' ) {
	        	$color = substr( $color, 1 );
	        }
	 
	        //Check if color has 6 or 3 characters and get values
	        if (strlen($color) == 6) {
	                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	        } elseif ( strlen( $color ) == 3 ) {
	                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	        } else {
	                return $default;
	        }
	 
	        //Convert hexadec to rgb
	        $rgb =  array_map('hexdec', $hex);
	 
	        //Check if opacity is set(rgba or rgb)
	        if($opacity){
	        	if(abs($opacity) > 1)
	        		$opacity = 1.0;
	        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	        } else {
	        	$output = 'rgb('.implode(",",$rgb).')';
	        }
	 
	        //Return rgb(a) color string
	        return $output;
	}

	public static function general_settings() {
		$args = get_option('zpm_general_settings');
		$defaults = [
			'project_access' => '0',
			'primary_color' => '#14aaf5',
			'primary_color_dark' => '#147be2',
			'primary_color_light' => '#60bbe9'
		];

		return wp_parse_args( $args, $defaults );
	}

	public static function generate_random_string($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public static function update_user_access( $user_id, $access = false ) {
		$settings = get_option( 'zpm_user_' . $user_id . '_settings' );
		$settings['can_zephyr'] = $access;
	    update_option( 'zpm_user_' . $user_id . '_settings', $settings );
	}

	public static function dismiss_notice( $notice_id ) {
		$notices = maybe_unserialize( get_option( 'zpm_notices', array() ) );
		$notices[$notice_id] = 'dismissed';
		update_option( 'zpm_notices', serialize( $notices ) );
	}

	public static function notice_is_dismissed( $notice_id ) {
		$notices = maybe_unserialize( get_option( 'zpm_notices', array() ) );
		if ( isset( $notices[$notice_id] ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_user_settings( $user_id ) {
		$settings = BaseController::get_project_manager_user( $user_id );
		$settings['can_zephyr'] = isset($settings['can_zephyr']) ? $settings['can_zephyr'] : "true";
		return (array) $settings;
	}

	/**
	* $args [
		'user_id' 		  => string,
		'profile_picture' => string,
		'name' 			  => string,
		'description' 	  => string,
		'email' 		  => string,
		'notify_activity' => int (1 | 0),
		'notify_tasks' 	  => int (1 | 0),
		'notify_updates'  => int (1 | 0),
		'hide_dashboard_widgets' => boolean
	]
	**/
	public static function save_user_settings( $user_id, $args ) {
		$defaults = Utillities::get_user_settings( $user_id );
		$settings = wp_parse_args( $args, $defaults );
		update_option( 'zpm_user_' . $user_id . '_settings', $settings );
	}

	public static function get_one_signal_device_ids() {
		$devices = maybe_unserialize( get_option( 'zpm_devices', array() ) );
		$device_ids = [];

		foreach ( (array) $devices as $device) {
			if ( isset( $device['one_signal_user_id'] ) ) {
				$device_ids[] = $device['one_signal_user_id'];
			}
		}

		return $device_ids;
	}
}