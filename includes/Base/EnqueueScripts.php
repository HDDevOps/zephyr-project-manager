<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Utillities;
use Inc\Base\AjaxHandler;
use Inc\Base\BaseController;

class EnqueueScripts extends AjaxHandler {

	public static function register() {
		add_action( 'admin_enqueue_scripts', array( 'Inc\Base\EnqueueScripts', 'enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( 'Inc\Base\EnqueueScripts', 'enqueue_user_scripts' ) );
	}
	/** 
	* Enqueue all admin scripts and styles
	*/
	public static function enqueue_admin_scripts($hook) {
		$user = BaseController::get_project_manager_user(get_current_user_id());
		$version = '1.9';
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		
	    wp_register_style( 'linearicons', ZPM_PLUGIN_URL . '/assets/css/linearicons.css' );
	    wp_register_style( 'jquery-ui-styles', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
	    wp_register_style( 'fullcalender_css', ZPM_PLUGIN_URL . '/assets/css/fullcalendar.css' );
	    $custom_css = EnqueueScripts::custom_styles();

	    $rest_url = get_rest_url();
	    $handle = curl_init( $rest_url );
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($handle);
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		if ($httpCode == 404) {
			$rest_url = get_home_url() . '/index.php/wp-json/';
		}

		curl_close($handle);
	    
	    wp_register_style( 'chosen_css', ZPM_PLUGIN_URL . '/assets/css/chosen.css' );
	    wp_enqueue_style( 'zpm-open-sans', '//fonts.googleapis.com/css?family=Roboto' ); 
	    wp_enqueue_style( 'linearicons' );
	    wp_enqueue_style( 'jquery-ui-styles' );
	    wp_enqueue_style( 'fullcalender_css' );
	    wp_enqueue_style( 'chosen_css' );
	    wp_enqueue_style( 'zpm-admin-styles', ZPM_PLUGIN_URL . '/assets/css/admin-styles.css', array(), '1.7' );
	    wp_add_inline_style( 'zpm-admin-styles', $custom_css);
		wp_enqueue_style( 'font-awesome', '//use.fontawesome.com/releases/v5.0.8/css/all.css' );
	    wp_register_script( 'moment', ZPM_PLUGIN_URL . '/assets/js/moment.min.js', array( 'jquery' ) );
	    wp_register_script( 'fullcalender_js', ZPM_PLUGIN_URL . '/assets/js/fullcalendar.js', array( 'jquery', 'moment' ) );
	    wp_register_script( 'chosen_js', ZPM_PLUGIN_URL . '/assets/js/chosen.jquery.js', array( 'jquery' ) );
	    wp_register_script( 'chartjs', ZPM_PLUGIN_URL . '/assets/js/chart.js', array( 'jquery' ) );
		wp_enqueue_script( 'chartjs' );
		
	    wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_script( 'moment' );
	    wp_enqueue_script( 'fullcalender_js', array( 'jquery', 'moment' ) ); 
	    wp_enqueue_script( 'chosen_js' ); 
	    wp_enqueue_script( 'zephyr-projects', ZPM_PLUGIN_URL . '/assets/js/zephyr-projects.js', array( 'jquery', 'fullcalender_js' ), $version );
	    wp_enqueue_script( 'zpm-core-admin', ZPM_PLUGIN_URL . '/assets/js/core-admin.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-datepicker' ), $version );
		wp_localize_script( 'zpm-core-admin', 'zpm_localized', array(
			'rest_url' 	 => $rest_url . 'zephyr_project_manager/v1/',
			'plugin_url' => ZPM_PLUGIN_URL,
			'tasks_url'  => esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')),
			'ajaxurl' 	 => admin_url( 'admin-ajax.php' ),
			'user_id' 	 => get_current_user_id(),
            'user_name'  => $user['name'],
            'users' 	 => get_users(),
            'wp_nonce'	 => wp_create_nonce('zpm_nonce')
		) );
		wp_register_script( 'zpm-progress-charts', ZPM_PLUGIN_URL . '/assets/js/progress-charts.js' );
		wp_enqueue_script( 'zpm-progress-charts' );

		wp_enqueue_script('heartbeat');
	}

	public static function enqueue_user_scripts() {

	}

	public static function custom_styles() {
		$general_settings = Utillities::general_settings();
		$primary = $general_settings['primary_color'];
		$primary_light = $general_settings['primary_color_light'];
		$primary_dark = $general_settings['primary_color_dark'];
		$html = "
			.zpm_button,
			.zpm_modal_body button {
				background: {$primary} !important;
			}
			.zpm_button:hover,
			.zpm_modal_body button:hover,
			.zpm_dropdown_list li:hover {
				background: {$primary_light} !important;
			}
			#zpm_add_new_btn {
				background: {$primary} !important;
			}
			#zpm_add_new_btn.active {
				background: {$primary_dark} !important;
			}
			.zpm_input:hover, 
			.zpm_input:focus, 
			.zpm_input:active,
			.zpm-modal .zpm_input:hover, 
			.zpm-modal .zpm_input:focus, 
			.zpm-modal .zpm_input:active,
			.chosen-container .chosen-single:hover,
			.chosen-container .chosen-single:focus,
			.chosen-container .chosen-single:active {
			    border-color: {$primary} !important;
			}
			.zpm_checkbox_label input:checked+.zpm_main_checkbox svg path {
			    fill: {$primary} !important;
			    stroke: {$primary} !important;
			}
			.zpm_project_name_input:focus, .zpm_project_name_input:active {
			    border-color: {$primary} !important;
			}
			.zpm_project_title:hover {
			    background: linear-gradient(45deg, {$primary_dark} 0%,{$primary} 100%);
			    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{$primary_dark}', endColorstr='{$primary}',GradientType=1 );
			    color: #fff;
			}
			.zpm_project_progress_bar,
			.zpm_nav_item_selected:after,
			.zpm_nav_item:hover:after {
			    background: {$primary} !important;
			}
			.zpm_fancy_item:hover, 
			.zpm_fancy_item:hover a,
			.zpm_nav_item_selected,
			.zpm_nav_item:hover {
				color: {$primary} !important;
			}
			button.zpm_button_outline {
			    background: none !important;
			    border: 1px solid {$primary} !important;
			    color: {$primary} !important;;
			}
			button.zpm_button_outline:hover {
				background: {$primary} !important;
				color: #fff;
			}
			.zpm-toggle:checked + .zpm-toggle-label:before {
			    background-color: {$primary_light};
			}

			.zpm-toggle:checked + .zpm-toggle-label:after {
			    background-color: {$primary};
			}
			.zpm_comment_attachment a:hover,
			.zpm_link {
			    color: {$primary};
			}
			.zpm_task_loader:after {
				border-color: {$primary} transparent {$primary} transparent;
			}
			.zpm_message_action_buttons #zpm_task_chat_files:hover, .zpm_message_action_buttons #zpm_task_chat_comment:hover {
			    background-color: {$primary} !important;
			}
			.zpm_message_action_buttons #zpm_task_chat_files, .zpm_message_action_buttons #zpm_task_chat_comment {
			    border: 1px solid {$primary} !important;
			    color: {$primary} !important;
			}
			.zpm_task_due_date {
				color: {$primary} !important;
			}
			.zpm_modal_list li:hover {
			    background: {$primary_light} !important;
			}
			.zpm_activity_date {
				background: {$primary} !important;
			}
			.zpm_tab_title.zpm_tab_selected,
			.zpm_tab_title:hover {
			    color: {$primary} !important;
			}
		";
		return $html;
	}

}