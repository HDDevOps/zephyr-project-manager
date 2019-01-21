<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Zephyr;
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
		$version = Zephyr::getPluginVersion();

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

		if ($httpCode == 404 || $response == false && strpos( $rest_url , 'localhost' ) !== false) {
			$rest_url = get_home_url() . '/index.php/wp-json/';
		}

		curl_close($handle);
	    
	    wp_register_style( 'chosen_css', ZPM_PLUGIN_URL . '/assets/css/chosen.css' );
	    wp_enqueue_style( 'zpm-open-sans', '//fonts.googleapis.com/css?family=Roboto' ); 
	    wp_enqueue_style( 'linearicons' );
	    wp_enqueue_style( 'jquery-ui-styles' );
	    wp_enqueue_style( 'fullcalender_css' );
	    wp_enqueue_style( 'chosen_css' );
	    wp_enqueue_style( 'zpm-admin-styles', ZPM_PLUGIN_URL . '/assets/css/admin-styles.css', array(), $version );
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

	    $localized_strings = array(
	    	'choose_image' => __( 'Choose Image', 'zephyr-project-manager' ),
	    	'delete_team_notice' => __( 'Are you sure you want to delete this team? This action cannot be undone.', 'zephyr-project-manager' ),
	    	'loading_progress' => __( 'Loading progress...', 'zephyr-project-manager' ),
	    	'completed_projects' => __( 'Completed Projects', 'zephyr-project-manager' ),
	    	'pending_projects' => __( 'Pending Projects', 'zephyr-project-manager' ),
	    	'saving_changes' => __( 'Saving changes...', 'zephyr-project-manager' ),
	    	'select_csv' => __( 'Select a CSV File', 'zephyr-project-manager' ),
	    	'select_json' => __( 'Select a JSON File', 'zephyr-project-manager' ),
	    	'choose_file' => __( 'Choose File', 'zephyr-project-manager' ),
	    	'tasks_completed' => __( 'Tasks Completed', 'zephyr-project-manager' ),
	    	'tasks_remaining' => __( 'Tasks Remaining', 'zephyr-project-manager' ),
	    	'no_projects_found' => __( 'Oops, no projects found.', 'zephyr-project-manager' ),
	    	'no_users_found' => __( 'Oops, no users found.', 'zephyr-project-manager' ),
	    	'no_teams_found' => __( 'Oops, no teams found.', 'zephyr-project-manager' ),
	    	'enter_task_name' => __( 'Please enter a task name', 'zephyr-project-manager' ),
	    	'enter_project_name' => __( 'Please enter a project name', 'zephyr-project-manager' ),
	    	'saving' => __( 'Saving...', 'zephyr-project-manager' ),
	    	'save_changes' => __( 'Save Changes', 'zephyr-project-manager' ),
	    	'add_task' => __( 'Add Task', 'zephyr-project-manager' ),
	    	'file_link' => __( 'File Link', 'zephyr-project-manager' ),
	    	'file_info' => __( 'File Info', 'zephyr-project-manager' ),
	    	'task' => __( 'Task', 'zephyr-project-manager' ),
	    	'date_uploaded' => __( 'Date Uploaded', 'zephyr-project-manager' ),
	    	'delete_file_notice' => __( 'Are you sure you want to delete this file?', 'zephyr-project-manager' ),
	    	'file_removed' => __( 'File Removed', 'zephyr-project-manager' ),
	    	'new_subtask' => __( 'New Subtask', 'zephyr-project-manager' ),
	    	'create_new_task' => __( 'Create New Task', 'zephyr-project-manager' ),
	    	'task_description' => __( 'Task Description', 'zephyr-project-manager' ),
	    	'assignee' => __( 'Assignee', 'zephyr-project-manager' ),
	    	'subtasks' => __( 'Subtasks', 'zephyr-project-manager' ),
	    	'attachments' => __( 'Attachments', 'zephyr-project-manager' ),
	    	'start_date' => __( 'Start Date', 'zephyr-project-manager' ),
	    	'due_date' => __( 'Due Date', 'zephyr-project-manager' ),
	    	'include' => __( 'Include', 'zephyr-project-manager' ),
	    	'task_name' => __( 'Task Name', 'zephyr-project-manager' ),
	    	'project_name' => __( 'Project Name', 'zephyr-project-manager' ),
	    	'copy_of' => __( 'Copy of', 'zephyr-project-manager' ),
	    	'copy_task' => __( 'Copy Task', 'zephyr-project-manager' ),
	    	'copy_project' => __( 'Copy Project', 'zephyr-project-manager' ),
	    	'create_new_project' => __( 'Create New Project', 'zephyr-project-manager' ),
	    	'name' => __( 'Name', 'zephyr-project-manager' ),
	    	'description' => __( 'Description', 'zephyr-project-manager' ),
	    	'tasks' => __( 'Tasks', 'zephyr-project-manager' ),
	    	'project' => __( 'Project', 'zephyr-project-manager' ),
	    	'convert_task' => __( 'Convert Tasks', 'zephyr-project-manager' ),
	    	'assignee_as_creator' => __( 'Assignee as Project creator', 'zephyr-project-manager' ),
	    	'subtasks_as_tasks' => __( 'Subtasks as Tasks', 'zephyr-project-manager' ),
	    	'task_description_as_description' => __( 'Task Description as Project Description', 'zephyr-projects' ),
	    	'convert_to_project' => __( 'Convert Task to Project', 'zephyr-project-manager' ),
	    	'loading_tasks' => __( 'Loading tasks...', 'zephyr-project-manager' ),
	    	'close' => __( 'Close', 'zephyr-project-manager' ),
	    	'files' => __( 'Files', 'zephyr-project-manager' ),
	    	'upload_file' => __( 'Upload File', 'zephyr-project-manager' ),
	    	'import_tasks' => __( 'Import Tasks', 'zephyr-project-manager' ),
	    	'importing_via_csv' => __( 'Importing via CSV', 'zephyr-project-manager' ),
	    	'importing_via_json' => __( 'Importing via JSON', 'zephyr-project-manager' ),
	    	'importing' => __( 'Importing', 'zephyr-project-manager' ),
	    	'sending' => __( 'Sending...', 'zephyr-project-manager' ),
	    	'comment' => __( 'Comment', 'zephyr-project-manager' ),
	    	'delete' => __( 'Delete', 'zephyr-project-manager' ),
	    	'deleted' => __( 'deleted', 'zephyr-project-manager' ),
	    	'subtask_saved' => __( 'Subtask Saved', 'zephyr-project-manager' ),
	    	'subtask_deleted' => __( 'Subtask Deleted', 'zephyr-project-manager' ),
	    	'creating_subtask' => __( 'Creating Subtask', 'zephyr-project-manager' ),
	    	'changes_saved' => __( 'Changes Saved Successfully', 'zephyr-project-manager' ),
	    	'message_removed' => __( 'Message Removed', 'zephyr-project-manager' ),
	    	'uploading_file' => __( 'Uploading File', 'zephyr-project-manager' ),
	    	'file_uploaded' => __( 'File Uploaded', 'zephyr-project-manager' ),
	    	'task_exists' => __( 'Task already exists', 'zephyr-project-manager' ),
	    	'deleting_category' => __( 'Deleting Category...', 'zephyr-project-manager' ),
	    	'creating_category' => __( 'Creating Category...', 'zephyr-project-manager' ),
	    	'overview' => __( 'Overview', 'zephyr-project-manager' ),
	    	'tasks' => __( 'Tasks', 'zephyr-project-manager' ),
	    	'discussion' => __( 'Discussion', 'zephyr-project-manager' ),
	    	'due_tasks' => __( 'Due Tasks', 'zephyr-project-manager' ),
	    	'pending_tasks' => __( 'Pending Tasks', 'zephyr-project-manager' ),
	    	'completed_tasks' => __( 'Completed Tasks', 'zephyr-project-manager' ),
	    	'delete_category_notice' => __( 'Are you sure you want to permanently delete this category?', 'zephyr-project-manager' ),
	    	'incorrect_import' => __( 'It appears that you have not uploaded a CSV file or a JSON file. Please make sure that the file format is correct and try again.', 'zephyr-project-manager' ),
	    	'delete_project_notice' => __( 'Are you sure you want to delete this project and all of its tasks?', 'zephyr-project-manager' ),
	    	'no_projects_created' => sprintf( __( 'No projects created yet. To create a project, click on the \'Add\' button at the top right of the screen or click %s here %s', 'zephyr-project-manager' ), '<a id="zpm_first_project" class="zpm_button_link">', '</a>' ),
	    	'error_loading_tasks' => __( 'There was a problem loading the tasks for this project.', 'zephyr-project-manager' ),
	    	'error_creating_task' => __(  'There was a problem creating the task', 'zephyr-project-manager' ),
	    	'error_saving_task' => __( 'There was a problem saving the task.', 'zephyr-project-manager' ),
	    	'error_creating_project' => __( 'There was a problem adding the project.', 'zephyr-project-manager' ),
	    	'error_removing_message' => __( 'Problem removing message.', 'zephyr-project-manager' ),
	    	'error_copying_task' => __( 'There was a problem copying the task. Please try again.', 'zephyr-project-manager' ),
	    	'error_converting_task' => __( 'There was a problem converting the task', 'zephyr-project-manager' ),
	    	'error_exporting_task' => __( 'There was a problem exporting the task. Please try again.', 'zephyr-project-manager' ),
	    	'error_exporting_tasks' => __( 'There was a problem exporting the tasks. Please try again.', 'zephyr-project-manager' ),
	    	'task_created' => __( 'Task successfully created', 'zephyr-project-manager' ),
	    	'task_upaded' => __( 'Task updated successfully.', 'zephyr-project-manager' ),
	    	'error_viewing_task' => __( 'There was a problem loading the task', 'zephyr-project-manager' ),
	    	'copying_project' => __( 'Copying project...', 'zephyr-project-manager' ),
	    	'error_copying_project' => __( 'There was an unexpected problem while copying the project', 'zephyr-project-manager' ),
	    	'project_copied' => __( 'Project successfully copied', 'zephyr-project-manager' ),
	    	'task_due_today' => __( 'You have a task that is due today', 'zephyr-project-manager' ),
	    	'task_due_tomorrow' => __( 'You have a task that is due tomorrow', 'zephyr-project-manager' ),
	    	'dismiss_notice' => __( 'Dismiss Notice', 'zephyr-project-manager' ),
	    	'select_assignee' => __( 'Select Assignee', 'zephyr-project-manager' ),
	    	'save_task' => __( 'Save Task', 'zephyr-project-manager' ),
	    	'loading' => __( 'Loading...', 'zephyr-project-manager' ),
	    	'creating_project' => __( 'Creating Project...', 'zephyr-project-manager' ),
	    	'project_created' => __( 'Project created successfully', 'zephyr-project-manager' ),
	    	'converting_to_project' => __( 'Converting task to project...', 'zephyr-project-manager' ),
	    	'new_project_created' => __( 'New project created', 'zephyr-project-manager' ),
	    	'go_to_project' => __( 'Go to Project', 'zephyr-project-manager' ),
	    	'no_date_set' => __( 'No date set', 'zephyr-project-manager' ),
	    	'printed_from_zephyr' => __( 'Printed from Zephyr Project Manager', 'zephyr-project-manager' ),
	    	'error_printing_tasks' => __( 'There was a problem printing the project.', 'zephyr-project-manager' ),
	    	'problem_occurred' => __( 'There was a problem. Please reload and try again.', 'zephyr-project-manager' ),
	    	'error_sending_message' => __( 'There was a problem sending your message. Please try again.', 'zephyr-project-manager' ),
	    	'error_exporting_project_csv' => __( 'There was a problem exporting the project to CSV.', 'zephyr-project-manager' ),
	    	'category_created' => __( 'Category created.', 'zephyr-project-manager' ),
	    	'error_creating_category' => __( 'There was a problem adding the category. Please try again.', 'zephyr-project-manager' ),
	    	'error_filtering' => __( 'There was a problem with the filtering.', 'zephyr-project-manager' ),
	    	'error_deleting_project' => __( 'There was a problem deleting the project.', 'zephyr-project-manager' ),
	    	'error_importing_file' => __( 'There was a problem importing the file. Please try again.', 'zephyr-project-manager' ),
	    	'category_deleted' => __( 'Category deleted.', 'zephyr-project-manager' ),

	    	'error_deleting_category' => __( 'There was a problem removing the category. Please try again.', 'zephyr-project-manager' ),
	    	'category_saved' => __( 'Category changes saved.', 'zephyr-project-manager' ),
	    	'error_saving_category' => __( 'There was a problem updating the category. Please try again.', 'zephyr-project-manager' ),
	    	'task_deleted' => __( 'Task deleted', 'zephyr-project-manager' ),
	    	'error_deleting_task' => __( 'Problem deleting task', 'zephyr-project-manager' ),
	    	'error_loading_project_tasks' => __( 'There was a problem loading the tasks for this project.', 'zephyr-project-manager' ),
	    	'removed_from_dashboard' => __( 'Project removed from the dashboard.', 'zephyr-project-manager' ),
	    	'added_to_dashboard' => __( 'Added to the dashboard.', 'zephyr-project-manager' ),
	    	'error_adding_to_dashboard' => __( 'There was a problem adding the project to the dashboard.', 'zephyr-project-manager' ),
	    	'adding_to_dashboard' => __( 'Removing project from dashboard.', 'zephyr-project-manager' ),
	    	'creating_task' => __( 'Creating task...', 'zephyr-project-manager' ),
	    	'members_saved' => __( 'Members saved', 'zephyr-project-manager' ),
	    	'project_status_saved' => __( 'Project status saved', 'zephyr-project-manager' ),
	    	'error_updating_status' => __( 'There was a problem updating the status.', 'zephyr-project-manager' ),
	    );

		wp_localize_script( 'zpm-core-admin', 'zpm_localized', array(
			'rest_url' 	 => $rest_url . 'zephyr_project_manager/v1/',
			'plugin_url' => ZPM_PLUGIN_URL,
			'tasks_url'  => esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')),
			'ajaxurl' 	 => admin_url( 'admin-ajax.php' ),
			'user_id' 	 => get_current_user_id(),
            'user_name'  => $user['name'],
            'users' 	 => get_users(),
            'wp_nonce'	 => wp_create_nonce('zpm_nonce'),
            'strings'	 => $localized_strings,
            'is_admin'	 => true
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