<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Zephyr;
use Inc\Api\Emails;
use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Activity;
use Inc\Core\Utillities;
use Inc\Core\Categories;
use Inc\Api\ColorPickerApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Core\Members;


class AjaxHandler extends BaseController {

	/**
	* Registers the callback functions responsible for providing a response
	* to Ajax requests setup throughout the rest of the plugin
	* @since    1.0.0
	*/

	public function __construct() {

		/* Projects */
		add_action( 'wp_ajax_zpm_new_project', array( $this, 'new_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_new_project', array( $this, 'new_project' ) );
	    add_action( 'wp_ajax_zpm_remove_project', array( $this, 'remove_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_project', array( $this, 'remove_project' ) );
	    add_action( 'wp_ajax_zpm_get_project', array( $this, 'get_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_project', array( $this, 'get_project' ) );
		add_action( 'wp_ajax_zpm_get_projects', array( $this, 'get_projects' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_projects', array( $this, 'get_projects' ) );
	    add_action( 'wp_ajax_zpm_save_project', array( $this, 'save_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_save_project', array( $this, 'save_project' ) );
	    add_action( 'wp_ajax_zpm_update_project_status', array( $this, 'update_project_status' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_project_status', array( $this, 'update_project_status' ) );
	    add_action( 'wp_ajax_zpm_like_project', array( $this, 'like_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_like_project', array( $this, 'like_project' ) );
	    add_action( 'wp_ajax_zpm_copy_project', array( $this, 'copy_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_copy_project', array( $this, 'copy_project' ) );
	    add_action( 'wp_ajax_zpm_export_project', array( $this, 'export_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_export_project', array( $this, 'export_project' ) );
	    add_action( 'wp_ajax_zpm_print_project', array( $this, 'print_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_print_project', array( $this, 'print_project' ) );
	    add_action( 'wp_ajax_zpm_project_progress', array( $this, 'project_progress' ) );
	    add_action( 'wp_ajax_nopriv_zpm_project_progress', array( $this, 'project_progress' ) );
	    add_action( 'wp_ajax_zpm_add_project_to_dashboard', array( $this, 'add_project_to_dashboard' ) );
	    add_action( 'wp_ajax_nopriv_zpm_add_project_to_dashboard', array( $this, 'add_project_to_dashboard' ) );
	    add_action( 'wp_ajax_zpm_remove_project_from_dashboard', array( $this, 'remove_project_from_dashboard' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_project_from_dashboard', array( $this, 'remove_project_from_dashboard' ) );

	    /* Tasks */
		add_action( 'wp_ajax_zpm_new_task', array( $this, 'new_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_new_task', array( $this, 'new_task' ) );
	    add_action( 'wp_ajax_zpm_view_task', array( $this, 'view_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_view_task', array( $this, 'view_task' ) );
		add_action( 'wp_ajax_zpm_copy_task', array( $this, 'copy_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_copy_task', array( $this, 'copy_task' ) );
		add_action( 'wp_ajax_zpm_export_task', array( $this, 'export_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_export_task', array( $this, 'export_task' ) );
		add_action( 'wp_ajax_zpm_export_tasks', array( $this, 'export_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_export_tasks', array( $this, 'export_tasks' ) );
	    add_action( 'wp_ajax_zpm_upload_tasks', array( $this, 'upload_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_upload_tasks', array( $this, 'upload_tasks' ) );
		add_action( 'wp_ajax_zpm_convert_to_project', array( $this, 'convert_to_project' ) );
	    add_action( 'wp_ajax_nopriv_zpm_convert_to_project', array( $this, 'convert_to_project' ) );
	    add_action( 'wp_ajax_zpm_update_task_completion', array( $this, 'update_task_completion' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_task_completion', array( $this, 'update_task_completion' ) );
	    add_action( 'wp_ajax_zpm_remove_task', array( $this, 'remove_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_task', array( $this, 'remove_task' ) );
	    add_action( 'wp_ajax_zpm_save_task', array( $this, 'save_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_save_task', array( $this, 'save_task' ) );
	    add_action( 'wp_ajax_zpm_get_tasks', array( $this, 'get_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_tasks', array( $this, 'get_tasks' ) );
	    add_action( 'wp_ajax_zpm_get_task', array( $this, 'get_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_task', array( $this, 'get_task' ) );
	    add_action( 'wp_ajax_zpm_filter_tasks', array( $this, 'filter_tasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_filter_tasks', array( $this, 'filter_tasks' ) );

	    add_action( 'wp_ajax_zpm_filter_tasks_by', array( $this, 'filter_by' ) );
	    add_action( 'wp_ajax_nopriv_zpm_filter_tasks_by', array( $this, 'filter_by' ) );

	    add_action( 'wp_ajax_zpm_like_task', array( $this, 'like_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_like_task', array( $this, 'like_task' ) );
	    add_action( 'wp_ajax_zpm_follow_task', array( $this, 'follow_task' ) );
	    add_action( 'wp_ajax_nopriv_zpm_follow_task', array( $this, 'follow_task' ) );
	    add_action( 'wp_ajax_zpm_update_subtasks', array( $this, 'update_subtasks' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_subtasks', array( $this, 'update_subtasks' ) );

	    add_action( 'wp_ajax_zpm_update_project_members', array( $this, 'update_project_members' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_project_members', array( $this, 'update_project_members' ) );

	    /* Categories */
	    add_action( 'wp_ajax_zpm_create_category', array( $this, 'create_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_create_category', array( $this, 'create_category' ) );
	    add_action( 'wp_ajax_zpm_remove_category', array( $this, 'remove_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_category', array( $this, 'remove_category' ) );
	    add_action( 'wp_ajax_zpm_update_category', array( $this, 'update_category' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_category', array( $this, 'update_category' ) );
	    add_action( 'wp_ajax_zpm_display_categories', array( $this, 'display_category_list' ) );
	    add_action( 'wp_ajax_nopriv_zpm_display_categories', array( $this, 'display_category_list' ) );

	    /* Comments & Messages */
	    add_action( 'wp_ajax_zpm_send_comment', array( $this, 'send_comment' ) );
	    add_action( 'wp_ajax_nopriv_zpm_send_comment', array( $this, 'send_comment' ) );
	    add_action( 'wp_ajax_zpm_remove_comment', array( $this, 'remove_comment' ) );
	    add_action( 'wp_ajax_nopriv_zpm_remove_comment', array( $this, 'remove_comment' ) );

	    /* Activity */
	    add_action( 'wp_ajax_zpm_display_activities', array( $this, 'display_activities' ) );
	    add_action( 'wp_ajax_nopriv_zpm_display_activities', array( $this, 'display_activities' ) );

	    add_action( 'wp_ajax_zpm_dismiss_notice', array( $this, 'dismiss_notice' ) );
	    add_action( 'wp_ajax_nopriv_zpm_dismiss_notice', array( $this, 'dismiss_notice' ) );

	    add_action( 'wp_ajax_zpm_update_user_access', array( $this, 'update_user_access' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_user_access', array( $this, 'update_user_access' ) );

	    add_action( 'wp_ajax_zpm_add_team', array( $this, 'add_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_add_team', array( $this, 'add_team' ) );

	    add_action( 'wp_ajax_zpm_update_team', array( $this, 'update_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_update_team', array( $this, 'update_team' ) );

	    add_action( 'wp_ajax_zpm_get_team', array( $this, 'get_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_get_team', array( $this, 'get_team' ) );

	    add_action( 'wp_ajax_zpm_delete_team', array( $this, 'delete_team' ) );
	    add_action( 'wp_ajax_nopriv_zpm_delete_team', array( $this, 'delete_team' ) );
	}

	/**
	* Ajax function for sending a comment/message/attachment
	* @return json
	*/
	public function send_comment() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');
		$user_id = isset($_POST['user_id']) ? sanitize_text_field( $_POST['user_id']) : $this->get_user_id();
		$subject_id = isset($_POST['subject_id']) ? sanitize_text_field( $_POST['subject_id']) : '';
		$message = isset($_POST['message']) ? serialize( sanitize_textarea_field($_POST['message']) ) : '';
		$type = isset($_POST['type']) ? serialize( $_POST['type']) : '';
		$parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
		$attachments = isset($_POST['attachments']) && !empty($_POST['attachments']) ? $_POST['attachments'] : false;
		$subject = isset($_POST['subject']) ? $_POST['subject'] : '';

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $subject_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$args = $settings;
		$args['attachments'] = $attachments;

		do_action( 'zpm_new_comment', $args );

		if ($subject !== '') {
			$wpdb->insert($table_name, $settings);
			$last_comment = $wpdb->insert_id;
		} else {
			$last_comment = false;
		}

		if ($attachments) {
			foreach ($attachments as $attachment) {
				$parent_id = (!$last_comment) ? '' : $last_comment;
				$attachment_type = ($subject == '' && $attachment['attachment_type'] !== '') ? $attachment['attachment_type'] : $subject;
				$subject_id = ($subject_id == '' && $attachment['subject_id'] !== '') ? $attachment['subject_id'] : $subject;
				$settings['user_id'] = $attachment_type;
				$settings['subject'] = $attachment_type;
				$settings['subject_id'] = $subject_id;
				$settings['parent_id'] = $parent_id;
				$settings['type'] = serialize('attachment');
				$settings['message'] = serialize($attachment['attachment_id']);
				$wpdb->insert($table_name, $settings);
			}
		}

		

		if ($subject == 'task') {
			$last_comment = Tasks::get_comment($last_comment);
			$response = array(
				'html' => Tasks::new_comment($last_comment)
			);
		} elseif ($subject == 'project') {
			$last_comment = Projects::get_comment($last_comment);
			$response = array(
				'html' => Projects::new_comment($last_comment)
			);
		} else {
			$html = Projects::file_html($attachments[0]['attachment_id'], $wpdb->insert_id);
			$response = [
				'html' => $html
			];
		}
		
		echo json_encode($response);
		die();
	}

	/* Project Ajax functions */
	public function new_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );

		$Project = new Projects();
		$data = array();
		if (isset($_POST['project_name'])) {
			$data['name'] = stripslashes(sanitize_text_field($_POST['project_name']));
		}
		if (isset($_POST['project_description'])) {
			$data['description'] = stripslashes(sanitize_textarea_field($_POST['project_description']));
		}
		if (isset($_POST['project_team'])) {
			$data['team'] = serialize($_POST['project_team']);
		}
		if (isset($_POST['project_categories'])) {
			$data['categories'] = serialize($_POST['project_categories']);
		}
		if (isset($_POST['project_start_date'])) {
			$data['date_start'] = sanitize_text_field($_POST['project_start_date']);
		}
		if (isset($_POST['project_due_date'])) {
			$data['date_due'] = serialize( sanitize_text_field($_POST['project_due_date']) );
		}

		$data['type'] = isset($_POST['type']) ? sanitize_text_field ($_POST['type']) : 'list';
		

		$last_id = Projects::new_project($data);
		$project = Projects::get_project($last_id);

		do_action( 'zpm_new_project', $project );
		Projects::update_progress($last_id);
		Emails::new_project_email($last_id);
		
		$response = array(
			'html' => Projects::new_project_cell($project),
			'frontend_html' => Projects::frontend_project_item($project)
		);
		echo json_encode($response);
		die();
	}

	/**
	* Removes a project from the database
	*/
	public function remove_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project = Projects::get_project( $_POST['project_id'] );
		Projects::delete_project( $_POST['project_id'] );
		do_action( 'zpm_project_deleted', $project );

		//Emails::deleted_project_email( $_POST['project_id'] );
		$return = array( 
			'project_count' => Projects::project_count()
		);
		echo json_encode($return);
		die();
	}

	/**
	* Ajax function to save changes made to a project settings
	*/
	public function save_project() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_PROJECTS_TABLE;
		$old_project = Projects::get_project($_POST['project_id']);
		$old_name = stripslashes($old_project->name);
		$old_description = $old_project->description;
		$date = date('Y-m-d H:i:s');
		$settings = array();
		$name = isset($_POST['project_name']) ? stripslashes(sanitize_text_field( $_POST['project_name'])) : '';
		$description = isset($_POST['project_description']) ? stripcslashes(sanitize_textarea_field( $_POST['project_description'])) : '';
		$start_date = isset($_POST['project_start_date']) ? sanitize_text_field( $_POST['project_start_date']) : '';
		$due_date = isset($_POST['project_due_date']) ? sanitize_text_field( $_POST['project_due_date']) : '';
		$categories = isset($_POST['project_categories']) ? serialize($_POST['project_categories']) : serialize([]);
		$settings = array(
			'name' 		  => $name,
			'description' => $description,
			'date_start'  => $start_date,
			'date_due'    => $due_date,
			'categories'  => $categories
		);
		$where = array(
			'id' => $_POST['project_id']
		);
		$wpdb->update( $table_name, $settings, $where );
		$last_id = $wpdb->insert_id;

		if ($old_name !== $settings['name']) {
			Activity::log_activity($this->get_user_id(), $_POST['project_id'], $old_name, $settings['name'], 'project', 'project_changed_name', $date);
		}

		if ($old_description !== $settings['description']) {
			Activity::log_activity($this->get_user_id(), $_POST['project_id'], '', $settings['name'], 'project', 'project_changed_description', $date);
		}

		echo json_encode(array(
			'response' => 'success',
			'categories' => $_POST['project_categories']
		));
		die();
	}

	public function update_project_status() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		
		$project_id = $_POST['project_id'];
		$status = isset($_POST['status']) ? sanitize_textarea_field($_POST['status']) : '';
		$status_color = isset($_POST['status_color']) ? sanitize_text_field($_POST['status_color']) : '';

		$data = array(
			'id' => $project_id,
			'status' => $status,
			'status_color' => $status_color
		);

		Projects::update_project_status($project_id, $status, $status_color);

		do_action('zpm_project_status_changed', $data);
		echo json_encode(array(
			'status' => 'success'
		));
		die();
	}

	public function update_project_members() {
		global $wpdb;
		$project_id = $_POST['project_id'];
		$members = $_POST['members'];
		Projects::update_members( $project_id, $members );
		echo json_encode( $members );
		die();
	}

	/**
	* Export a project to CSV or JSON and download the file
	* @return json
	*/
	public function export_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project = Projects::get_project($_POST['project_id']);
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			$tasks = Tasks::get_project_tasks($_POST['project_id'], true);
			$project->tasks = $tasks;
			$formattedData = json_encode($project);
			$filename = $upload_dir['basedir'] . '/Project - ' . stripslashes($project->name) . '.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/Project - ' . stripslashes($project->name) . '.json';
			$response = array(
				'file_name' => 'Project - ' . stripslashes($project->name) . '.json',
				'file_url'  => $filename
			);
			echo json_encode($response);
		} else {
			$filename = $upload_dir['basedir'] . '/Project - ' . $project->name . '.csv';	 
			$filename = fopen($filename, 'w');
			fputcsv($filename, array('ID', 'User ID', 'Name', 'Description', 'Completed', 'Team', 'Categories', 'Date Created', 'Date Due', 'Date Start', 'Date Completed', 'Other Data'));
			fputcsv($filename, (array) $project);
			$filename = $upload_dir['baseurl'] . '/Project - ' . $project->name . '.csv';

			// Download project tasks to CSV as well
			$tasks = Tasks::get_project_tasks($_POST['project_id'], true);
			$tasks_file = $upload_dir['basedir'] . '/' . $project->name . ' - Tasks.csv';
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="project_tasks.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			 
			$tasks_file = fopen($tasks_file, 'w');

			fputcsv($tasks_file, array('ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At'));
	
			// save each row of the data
			foreach ($tasks as $row) {
				fputcsv($tasks_file, get_object_vars($row));
			}

			$tasks_file = $upload_dir['baseurl'] . '/' . $project->name . ' - Tasks.csv';
			
			$files = array(
				'project_csv' => $filename,
				'project_tasks_csv' => $tasks_file,
			);

			echo json_encode($files);
		}

		die();
	}

	/**
	* Print a project
	* @return json
	*/
	public function print_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = $_POST['project_id'];
		$project = Projects::get_project($project_id);
		$project_tasks = Tasks::get_project_tasks($project_id);
		$data = array();
		$data['project'] = $project;

		foreach ($project_tasks as $project_task) {
			$user = BaseController::get_user_info($project_task->assignee);
			$project_task->username = $user;
			$data['tasks'][] = $project_task;
		}

		echo json_encode($data);
		die();
	}

	/**
	* Get the data for a project chart
	* @return json
	*/
	public function project_progress() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$task_count = Tasks::get_project_task_count($project_id);
		$completed_tasks = Tasks::get_project_completed_tasks($project_id);
		$args = array( 'project_id' => $project_id );
		$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
		$pending_tasks = $task_count - $completed_tasks;
		$percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100): '100';
		$chart_data = get_option('zpm_chart_data', array());
		$response = array(
			'chart_data' => $chart_data[$project_id]
		);
		echo json_encode($response);
		die();
	}

	/**
	* Get a single project
	* @return json
	*/
	public function get_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$Tasks = new Tasks();
		$project_id = $_POST['project_id'];
		$project = Projects::get_project($project_id);
		$comments = Projects::get_comments($project_id);
		$categories = unserialize($project->categories);
		$comments_html = '';

		foreach ($comments as $comment) {
			$html = Projects::new_comment($comment);
			$comments_html .= $html;
		}

		$start_date = new DateTime($project->date_start);
		$due_date = new DateTime($project->date_due);

		$total_tasks = $Tasks->get_project_task_count( $project->id );
		$completed_tasks = $Tasks->get_project_completed_tasks( $project->id );
		$active_tasks = (int) $total_tasks - (int) $completed_tasks;
		$message_count = sizeof( $comments );

		ob_start();
		?>

			<span id="zpm_project_modal_dates" class="zpm_project_overview_section">
				<span id="zpm_project_modal_start_date">
					<label class="zpm_label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_project_date"><?php echo $start_date->format('d M'); ?></span>
				</span>

				<span id="zpm_project_modal_due_date">
					<label class="zpm_label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?>:</label>
					<span class="zpm_project_date"><?php echo $due_date->format('d M'); ?></span>
				</span>
			</span>

			<div id="zpm_project_progress">
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $completed_tasks; ?></p>
					<p><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
				</span>
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $active_tasks; ?></p>
					<p><?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?></p>
				</span>
				<span class="zpm_project_stat">
					<p class="zpm_stat_number"><?php echo $message_count; ?></p>
					<p><?php _e( 'Message', 'zephyr-project-manager' ); ?></p>
				</span>
			</div>

			<span id="zpm_project_modal_description" class="zpm_project_overview_section">
				<label class="zpm_label"><?php _e( 'Description', 'zephyr-project-manager' ); ?>:</label>
				<p class="zpm_description"><?php echo $project->description; ?></p>
			</span>

			<span id="zpm_project_modal_categories" class="zpm_project_overview_section">
				<label class="zpm_label"><?php _e( 'Categories', 'zephyr-project-manager' ); ?>:</label>
				<?php foreach ($categories as $category) : ?>
					<?php $category = Categories::get_category($category); ?>
					<span class="zpm_project_category"><?php echo $category->name; ?></span>
				<?php endforeach; ?>
			</span>

			
		<?php 
		$overview_html = ob_get_clean();

		$project->overview_html = $overview_html;
		$project->comments_html = $comments_html;

		echo json_encode($project);
		die();
	}

	/**
	* Returns a list of all projects
	* @return json
	*/
	public function get_projects() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$projects = Projects::get_projects();
		echo json_encode($projects);
		die();
	}

	/**
	* Like/hearts a project
	* @return json
	*/
	public function like_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = $_POST['project_id'];
		$user_id = $this->get_user_id();
		$liked_projects = unserialize(get_option( 'zpm_liked_projects_' . $user_id, false ));

		if (!$liked_projects) {
			$liked_projects = array();
		}

		if (!in_array($project_id, $liked_projects)) {
			$liked_projects[] = $project_id;
		} else {
			$liked_projects = array_diff($liked_projects, [$project_id]);
		}

		$liked_projects = serialize($liked_projects);
		update_option( 'zpm_liked_projects_' . $user_id, $liked_projects );
		echo json_encode($liked_projects);
		die();
	}

	/**
	* Copies a project and adds the copy in the database
	* @return json
	*/
	public function copy_project() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = (isset($_POST['project_id'])) ? $_POST['project_id'] : '';
		$copy_options = (isset($_POST['copy_options'])) ? $_POST['copy_options'] : '';
		$name = isset($_POST['project_name']) ? sanitize_text_field( $_POST['project_name']) : '';
		$args = [
			'project_id' => $project_id,
			'project_name' => $name,
			'copy_options' => $copy_options,
		];
		$last_project = Projects::copy_project($args);

		$response = array(
			'html' => Projects::new_project_cell($last_project)
		);
		echo json_encode($response);
		die();
	}

	/**
	* Ajax function to add a project to the dashboard
	* @uses $_POST['project_id'] Project ID
	* @return string
	*/
	public function add_project_to_dashboard() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : false;
		if ($project_id) {
			Projects::add_to_dashboard($project_id);
		}
		return 'Success';
	}

	/**
	* Ajax function to remove a project from the dashboard
	* @uses $_POST['project_id'] Project ID
	* @return string
	*/
	public function remove_project_from_dashboard() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : false;
		if ($project_id) {
			Projects::remove_from_dashboard($project_id);
		}
		return 'Success';
	}

	/**
	* Create a new task and save it in the database
	* @return json
	*/
	public function new_task() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$assignee = isset($_POST['task_assignee']) && $_POST['task_assignee'] !== '-1' ? $_POST['task_assignee'] : '-1';
		$project = isset($_POST['task_project']) ? $_POST['task_project'] : '';
		$name = isset($_POST['task_name']) ? stripslashes(sanitize_text_field( $_POST['task_name'])) : '';
		$description = isset($_POST['task_description']) ? stripslashes(sanitize_textarea_field( $_POST['task_description'])) : '';
		$date = date('Y-m-d H:i:s');
		$date_due = isset($_POST['task_due_date']) ? sanitize_text_field( $_POST['task_due_date']) : '';
		$date_start = isset($_POST['task_start_date']) ? sanitize_text_field( $_POST['task_start_date']) : $date;

		$team = isset($_POST['team']) ? $_POST['team'] : '';

		$settings = array(
			'user_id' 	  	 => $this->get_user_id(),
			'parent_id'		 => '-1',
			'assignee' 	  	 => $assignee,
			'project' 	  	 => $project,
			'name' 		  	 => $name,
			'description' 	 => $description,
			'completed'   	 => false,
			'date_start'  	 => $date_start,
			'date_due' 	  	 => $date_due,
			'date_created' 	 => $date,
			'date_completed' => '',
			'team'			 => $team
		);

		if ( Zephyr::isPro() ) {
			$settings['custom_fields'] = isset($_POST['task_custom_fields']) ? serialize( $_POST['task_custom_fields']) : '';
			$settings['kanban_col'] = isset($_POST['kanban_col']) ? sanitize_text_field( $_POST['kanban_col']) : '';
		}
		$frontend_settings = get_option('zpm_frontend_settings');

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;
		$task = Tasks::get_task($last_id);
		$Project = new Projects();
		$due_date = new DateTime($task->date_due);
		$task->date_due = $due_date->format('Y') !== '-0001' ? $due_date->format('d M') : '';
		$task_project = $Project->get_project($task->project);
		$task->project_name = is_object($task_project) ? $task_project->name : '';

		do_action( 'zpm_new_task', $task );

		Activity::log_activity($settings['user_id'], $last_id, '', $name, 'task', 'task_added', $date);
		Activity::log_activity($settings['user_id'], $last_id, '', $name, 'task', 'task_assigned', $date);
		
		if ($task->assignee !== "-1" && $task->assignee !== '') {
			Emails::new_task_email($last_id, $task->assignee);
		}

		Projects::update_progress( $project );

		$date = new DateTime($task->date_due);
    	$assignee = BaseController::get_project_manager_user($task->assignee);
    	$frontend = isset($_POST['frontend']) ? true : false;
    	ob_start();
    	?>
		<div class="zpm_kanban_item <?php echo $task->completed ? 'complete' : ''; ?>" data-task-id="<?php echo $task->id; ?>">
			<div class="zpm-kanban-task-info">
				<input type="checkbox" class="zpm-kanban-complete-task" id="zpm-kanban-complete-<?php echo $task->id; ?>" /><label class="zpm-kanban-complete-task-label lnr lnr-checkmark-circle" for="zpm-kanban-complete-<?php echo $task->id; ?>"></label>
				<span class="zpm-kanban-task-assignee <?php echo empty($assignee['id']) ? 'unassigned' : ''; ?>" style="background-image: url(<?php echo !empty($assignee['id']) ? $assignee['avatar'] : ''; ?>);"><?php echo empty($assignee['id']) ? 'Unassigned' : ''; ?></span>
				<span class="zpm-kanban-due"><?php echo $date->format('Y') !== '-0001' ? $date->format('d M') : ''; ?></span>
			</div>
			
			<textarea class="zpm_kanban_title" placeholder="<?php _e( 'Task Name', 'zephyr-project-manager' ); ?>" data-task-id="<?php echo $task->id; ?>"><?php echo $task->name ?></textarea>
			
			<div class="zpm-kanban-footer">
				<a class="zpm-link" href="<?php echo !$frontend ? esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id=' . $task->id)) : get_page_link($frontend_settings['front_page']) . '?action=task&id=' . $task->id; ?>" target="_blank"><?php _e( 'View Task', 'zephyr-project-manager' ); ?></a>
			</div>
		</div>
		<?php
		$kanban_html = ob_get_clean();
		
		$Tasks = new Tasks();
		$task->new_task_html = $Tasks->new_task_row($task);
		$task->kanban_html = $kanban_html;

		echo json_encode($task);
		die();
	}

	/**
	* Loads the content for the view task modal
	* @return json
	*/
	public function view_task() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '-1';
		ob_start();
		Tasks::view_task_modal($task_id);
		$html = ob_get_clean();
		echo $html;
		die();
	}

	/**
	* Copies a task and adds the duplicate in the database
	* @return json
	*/
	public function copy_task() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = (isset($_POST['task_id'])) ? $_POST['task_id'] : '';
		$task = Tasks::get_task($task_id);
		$copy_options =  (isset($_POST['copy_options'])) ? $_POST['copy_options'] : '';
		$date = date('Y-m-d H:i:s');
		$user_id = $this->get_user_id();
		$assignee = in_array('assignee', $copy_options) ? $task->assignee : $date;
		$name = isset($_POST['task_name']) ? sanitize_text_field($_POST['task_name']) : '';
		$description = in_array('description', $copy_options) ? $task->description : '';
		$date_start = in_array('start_date', $copy_options) ? $task->date_start : $date;
		$date_due = in_array('due_date', $copy_options) ? $task->date_due : '';
		$settings = array(
			'user_id' 		 => $user_id,
			'assignee' 		 => $assignee,
			'project' 		 => $task->project,
			'name' 			 => $name,
			'description' 	 => $description,
			'completed' 	 => $task->completed,
			'date_start' 	 => $date_start,
			'date_due' 		 => $date_due,
			'date_created' 	 => $date,
			'date_completed' => ''
		);

		

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		$subtasks = Tasks::get_subtasks($task_id);

		foreach ($subtasks as $subtask) {
			$settings = array(
				'parent_id'		 => $last_id,
				'user_id' 		 => $user_id,
				'assignee' 		 => $subtask->assignee,
				'project' 		 => $subtask->project,
				'name' 			 => $subtask->name,
				'completed' 	 => $subtask->completed,
				'date_start' 	 => $subtask->date_start,
				'date_due' 		 => $subtask->date_due,
				'date_created' 	 => $subtask->date_created,
				'date_completed' => ''
			);

			$wpdb->insert( $table_name, $settings );
		}

		$new_task = Tasks::get_task($last_id);
		$response = array(
			'html' => Tasks::new_task_row($new_task)
		);

		Projects::update_progress( $task->project );

		echo json_encode($response);
		die();
	}

	/**
	* Exports a task to a JSON or CSV file
	*
	* @return json
	*/
	public function export_task() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$task = Tasks::get_task($_POST['task_id']);
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			// Save JSON file
			$data = array($task);
			$formattedData = json_encode($data);
			$filename = $upload_dir['basedir'] . '/Task - ' . $task->name . '.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/Task - ' . $task->name . '.json';
			$response = [
				'file_url'  => $filename,
				'file_name' => 'Task - ' . $task->name . '.json'
			];
			echo json_encode($response);
		} else {
			$filename = $upload_dir['basedir'] . '/Task - ' . $task->name . '.csv';	 
			$filename = fopen($filename, 'w');
			fputcsv($filename, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At'));
			fputcsv($filename, (array) $task);
			$filename = $upload_dir['baseurl'] . '/Task - ' . $task->name . '.csv';
			$response = [
				'file_url'  => $filename,
				'file_name' => 'Task - ' . $task->name . '.csv'
			];
			echo json_encode($response);
		}
		die();
	}

	// Exports all tasks
	public function export_tasks() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$tasks = Tasks::get_tasks();
		$upload_dir = wp_upload_dir();

		if (isset($_POST['export_to']) && $_POST['export_to'] == 'json') {
			
			$formattedData = json_encode($tasks);
			$filename = $upload_dir['basedir'] . '/All Tasks.json';
			$handle = fopen($filename,'w+');
			fwrite($handle, $formattedData);
			fclose($handle);
			$filename = $upload_dir['baseurl'] . '/All Tasks.json';
			echo json_encode($filename);
		} else {
			$filename = $upload_dir['basedir'] . '/All Tasks.csv';
			// save the column headers

			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="all_tasks.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			 
			$filename = fopen($filename, 'w');

			fputcsv($filename, array('ID', 'Parent ID', 'Created By', 'Project', 'Assignee', 'Task Name', 'Task Description', 'Categories', 'Completed', 'Created At', 'Start Date', 'Due Date', 'Completed At'));
	
			// save each row of the data
			foreach ($tasks as $row) {
				fputcsv($filename, get_object_vars($row));
			}
			$filename = $upload_dir['baseurl'] . '/All Tasks.csv';
			echo json_encode($filename);
		}
		
		die();
	}

	// Import Tasks
	public function upload_tasks() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$html = '';
		$filename = $_POST['zpm_file'];
		$file_type = $_POST['zpm_import_via'];
		$table_name = ZPM_TASKS_TABLE;
	
		if ($file_type == 'csv') {
			$row = 1;
			$taskArray = array();
			if (($handle = fopen($filename, "r")) !== FALSE) {
			    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			        $num = count($data);
			        $task = array(
			        	'id' 			 => $data[0],
			        	'parent_id' 	 => $data[1], 
			        	'user_id' 		 => $data[2],
			        	'project' 		 => $data[3],
			        	'assignee' 		 => $data[4],
			        	'name' 			 => $data[5],
			        	'description' 	 => $data[6],
			        	'categories' 	 => $data[7],
			        	'completed' 	 => $data[8],
			        	'date_created' 	 => $data[9],
			        	'date_start' 	 => $data[10],
			        	'date_due' 		 => $data[11],
			        	'date_completed' => $data[12]
			        );

			        if (!Tasks::task_exists($data[0])) {
			        	$wpdb->insert( $table_name, $task );
			        	$task = Tasks::get_task($wpdb->insert_id);
			        	if ($row > 1) {
			        		$html .= Tasks::new_task_row($task);
			        	}	
			        } else {
			        	$task['already_uploaded'] = true;
			        }

			        $row++;

			        $taskArray[] = $task;
			    }
			    fclose($handle);
			}
			$response = [
				'tasks' => $taskArray,
				'html' => $html
			];
			echo json_encode($response);
		} elseif ($file_type == 'json') {
			$json = file_get_contents($filename);
			$json_array = json_decode($json, true);
			$taskArray = array();

			foreach ($json_array as $task) {
				$task = array(
		        	'id' 			 => $task['id'],
		        	'parent_id' 	 => $task['parent_id'], 
		        	'user_id' 		 => $task['user_id'], 
		        	'project' 		 => $task['project'],
		        	'assignee' 		 => $task['assignee'],
		        	'name' 			 => $task['name'],
		        	'description' 	 => $task['description'],
		        	'categories' 	 => $task['categories'],
		        	'completed' 	 => $task['completed'],
		        	'date_created' 	 => $task['date_created'],
		        	'date_start' 	 => $task['date_start'],
		        	'date_due' 		 => $task['date_due'],
		        	'date_completed' => $task['date_completed']
		        );

		        if (!Tasks::task_exists($task['id'])) {
		        	$wpdb->insert( $table_name, $task );
		        	$task = Tasks::get_task($wpdb->insert_id);
		        	$html .= Tasks::new_task_row($task);
		        } else {
		        	$task['already_uploaded'] = true;
		        }
		        $taskArray[] = $task;
			}
			$response = [
				'tasks' => $taskArray,
				'html' => $html
			];
			echo json_encode($response);
		}
		die();
	}

	// Converts a given task to a project
	public function convert_to_project() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$task_id = (isset($_POST['task_id'])) ? $_POST['task_id'] : '';
		$task = Tasks::get_task($task_id);
		$convert_options =  (isset($_POST['convert_options'])) ? $_POST['convert_options'] : '';

		$subtasks = (in_array('subtasks', $convert_options)) ? Tasks::get_subtasks($task_id) : '';

		$table_name = ZPM_PROJECTS_TABLE;
		$date = date('Y-m-d H:i:s');
		$settings = array();
		$settings['user_id'] = $this->get_user_id();
		$settings['name'] = (isset($_POST['project_name'])) ? sanitize_text_field( $_POST['project_name']) : '';
		$settings['description'] = (in_array('description', $convert_options)) ? $task->description : '';
		$settings['completed'] = false;
		$settings['date_due'] = $task->date_start;
		$settings['date_created'] = $date;
		$settings['date_completed'] = '';
		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		if (is_array($subtasks)) {
			$tasks_table = ZPM_TASKS_TABLE;
			foreach ($subtasks as $subtask) {
				$task_settings = array();
				$task_settings['parent_id'] = '-1';
				$task_settings['user_id'] = $this->get_user_id();
				$task_settings['assignee'] = $this->get_user_id();
				$task_settings['project'] = $last_id;
				$task_settings['name'] = $subtask->name;
				$task_settings['description'] = '';
				$task_settings['completed'] = false;
				$task_settings['date_start'] = $date;
				$task_settings['date_due'] = '';
				$task_settings['date_created'] = $date;
				$task_settings['date_completed'] = '';
				$wpdb->insert( $tasks_table, $task_settings );
			}
		}
		
		$project = new Projects();
		$new_project = $project->get_project($last_id);
		echo json_encode($new_project);
		die();
	}

	public function update_task_completion() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$date = date('Y-m-d H:i:s');
		$task_id = isset($_POST['id']) ? $_POST['id'] : '-1';
		
		$settings = array(
			'completed' 		=> $_POST['completed'],
			'date_completed' 	=> $date
		);

		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		$task = Tasks::get_task($task_id);
		$completed_project_tasks = Tasks::get_project_completed_tasks( $task->project );
		$project_tasks = Projects::get_project_tasks( $task->project );

		if ( sizeof( $completed_project_tasks ) == $project_tasks ) {
			$completed = '1';
		} else {
			$completed = '0';
		}

		do_action( 'zpm_task_status_changed', $task );

		Projects::mark_complete( $task->project, $completed );
		Projects::update_progress( $task->project );
		die();
	}

	public function remove_task() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task = Tasks::get_task($_POST['task_id']);
		$date = date('Y-m-d H:i:s');

		$settings = array(
			'id' => $_POST['task_id']
		);
		Emails::delete_task_email( $_POST['task_id'] );
		$wpdb->delete( $table_name, $settings, [ '%d' ] );

		
		Activity::log_activity($this->get_user_id(), $_POST['task_id'], '', $task->name, 'task', 'task_deleted', $date);

		echo 'Success';
		die();
	}

	// Function that saves changes made to the task
	public function save_task() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
		$old_task = Tasks::get_task( $task_id );
		$settings = array();
		$settings['name'] 		 = (isset($_POST['task_name'])) ? stripslashes(sanitize_text_field( $_POST['task_name'])) : '';
		$settings['description'] = (isset($_POST['task_description'])) ? stripslashes(sanitize_textarea_field( $_POST['task_description'])) : '';
		$settings['assignee'] = (isset($_POST['task_assignee'])) ? sanitize_text_field( $_POST['task_assignee']) : $this->get_user_id();
		$settings['date_due'] = (isset($_POST['task_due_date'])) ? sanitize_text_field( $_POST['task_due_date']) : '';
		$settings['date_start'] = (isset($_POST['task_start_date'])) ? sanitize_text_field( $_POST['task_start_date']) : '';
		$settings['project'] = (isset($_POST['task_project'])) ? $_POST['task_project'] : '-1';
		$settings['team'] = (isset($_POST['team'])) ? $_POST['team'] : '';

		if ( Zephyr::isPro() ) {
			$settings['custom_fields'] = isset($_POST['task_custom_fields']) ? serialize( $_POST['task_custom_fields']) : '';
		}

		$where = array(
			'id' => $task_id
		);

		$wpdb->update( $table_name, $settings, $where );

		$date = date('Y-m-d H:i:s');

		if ($old_task->name !== $settings['name']) {
			Activity::log_activity($this->get_user_id(), $task_id, $old_task->name, $settings['name'], 'task', 'task_changed_name', $date);
		}

		if ($old_task->date_due !== $settings['date_due']) {
			Activity::log_activity($this->get_user_id(), $task_id, $settings['name'], $settings['date_due'], 'task', 'task_changed_date', $date);
			$date_due = new DateTime( $settings['date_due'] );
			Emails::task_date_change_email( $task_id, $settings['name'], $date_due );
		}

		echo json_encode($wpdb->insert_id);
		die();
	}

	// Marks a task as liked
	public function like_task() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$task_id = $_POST['task_id'];
		$user_id = $this->get_user_id();
		$liked_tasks = get_option( 'zpm_liked_tasks_' . $user_id, false );
		$liked_tasks = unserialize($liked_tasks);

		if (!$liked_tasks) {
			$liked_tasks = array();
		}

		if (!in_array($task_id, $liked_tasks)) {
			$liked_tasks[] = $task_id;
		} else {
			$liked_tasks = array_diff($liked_tasks, [$task_id]);
		}

		$liked_tasks = serialize($liked_tasks);
		update_option( 'zpm_liked_tasks_' . $user_id, $liked_tasks );

		echo json_encode($liked_tasks);
		die();
	}

	/**
	* Ajax function to follow a task
	* @return json
	*/
	public function follow_task() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$task_id = $_POST['task_id'];
		$user_id = $this->get_user_id();
		$followed_tasks = get_option( 'zpm_followed_tasks_' . $user_id, false );
		$followed_tasks = unserialize($followed_tasks);

		if (!$followed_tasks) {
			$followed_tasks = array();
		}

		if (!in_array($task_id, $followed_tasks)) {
			$followed_tasks[] = $task_id;
		} else {
			$followed_tasks = array_diff($followed_tasks, [$task_id]);
		}

		$followed_tasks = serialize($followed_tasks);
		update_option( 'zpm_followed_tasks_' . $user_id, $followed_tasks );
		$user = BaseController::get_project_manager_user($user_id);
		$html = '<span class="zpm_task_follower" data-user-id="' . $user['id'] . '" title="' . $user['name'] . '" style="background-image: url(' . $user['avatar'] . ');"></span>';
		$following = in_array($task_id, unserialize($followed_tasks)) ? true : false;
		$response = array(
			'html' 		=> $html,
			'following' => $following,
			'user_id'   => $user_id
		);
		echo json_encode($response);
		die();
	}

	/**
	* Updates the subtasks (add new subtask | delete subtask | update subtask name)
	* @param int $_POST['task_id']
	* @param string $_POST['subtask_action']
	* @param string $_POST['subtask_name']
	* @return json
	*/
	public function update_subtasks() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_TASKS_TABLE;
		$task_id = $_POST['task_id'];
		$action = $_POST['subtask_action'];

		switch ($action) {
			case 'new_subtask':
				$subtask_name = isset($_POST['subtask_name']) ? sanitize_text_field($_POST['subtask_name']) : '';
				$parent_task = Tasks::get_task($task_id);
				$date = date('Y-m-d H:i:s');
		
				$settings = array(
					'parent_id' 	 => $parent_task->id,
					'user_id' 		 => $parent_task->user_id,
					'assignee' 		 => $parent_task->assignee,
					'project' 		 => $parent_task->project,
					'name' 			 => $subtask_name,
					'description' 	 => '',
					'completed' 	 => false,
					'date_start' 	 => $parent_task->date_start,
					'date_due' 		 => $parent_task->date_due,
					'date_created' 	 => $date,
					'date_completed' => ''
				);

				$wpdb->insert( $table_name, $settings );
				$response = array(
					'name' => $subtask_name,
					'id' => $wpdb->insert_id
				); 
				echo json_encode($response);
				break;
			
			case 'delete_subtask':
				$subtask_id = $_POST['subtask_id'];
				$settings = array(
					'id' => $subtask_id
				);
				$wpdb->delete( $table_name, $settings, [ '%d' ] );
				$return = array(
					'success' => true
				);
				echo json_encode($return);
				break;

			case 'update_subtask':
				$new_subtask_name = isset($_POST['new_subtask_name']) ? sanitize_text_field($_POST['new_subtask_name']) : '';
				
				$settings = array(
					'name' => $new_subtask_name
				);

				$where = array(
					'id' => $_POST['subtask_id']
				);

				$wpdb->update( $table_name, $settings, $where );

				echo json_encode($subtasks);
				break;
			default:
				break;
		}

		die();
	}

	public function get_tasks() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$project_id = $_POST['project_id'];
		Tasks::view_task_list();
		die();
	}

	public function get_task() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$user_id = $this->get_user_id();
		$task_id = $_POST['task_id'];
		$task_data = Tasks::get_task($task_id);

		$followed_tasks = get_option( 'zpm_followed_tasks_' . $user_id, false );
		$followed_tasks = unserialize($followed_tasks);
		$following = in_array($task_id, $followed_tasks);

		$task_data->following = $following;
		$task_data->subtasks = Tasks::get_subtasks($task_id);
		echo json_encode($task_data);
		die();
	}

	public function filter_by() {
		$filter = $_POST['filter'];
		$current_filter = $_POST['current_filter'];
		$user_id = get_current_user_id();
		$tasks = array();

		switch ($current_filter) {
			case '-1':
				$tasks = Tasks::get_tasks();
				break;
			case '0':
				$tasks = Tasks::get_user_tasks( $user_id );
				break;
			case '1':
				$tasks = Tasks::get_completed_tasks( 0 );
				break;
			case '2':
				$tasks = Tasks::get_completed_tasks( 1 );
				break;	
			default:
				break;
		}

		$sorted_array = array();
		$tasks = (array) $tasks;

		switch ($filter) {
			case 'created':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_created));
				    $sorted_array[$task->date_created . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'start':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_start));
				    $sorted_array[$task->date_start . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'due':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->date_due));
				    $sorted_array[$task->date_due . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;
			case 'assignee':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->assignee));
				    $sorted_array[$task->assignee . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				krsort($tasks);
				break;	
			case 'name':
				foreach($tasks as $task){
				    $key = date('Y-m-d',strtotime($task->name));
				    $sorted_array[$task->name . Utillities::generate_random_string(6)] = $task;
				}
				$tasks = $sorted_array;
				ksort($tasks);
				break;	
			default:
				break;
		}

		$html = '';

		$frontend = isset($_POST['frontend']) ? $_POST['frontend'] : false;
		foreach ($tasks as $task) {
			$new_row = Tasks::new_task_row($task);
			if (!$frontend) {
				$html .= $new_row;
			} else {
				$html .= '<a href="?action=task&id=' . $task->id . '">' . $new_row . '</a>';
			}
		}

		if (empty($tasks)) {
			$html = '<p class="zpm_error_message">' . __( 'No results found...', 'zephyr-project-manager' ) . '</p>';
		}

		$response = array(
			'html' => $html
		);

		echo json_encode($response);
		die();
	}

	/**
	* Filters tasks based on users selection
	*/
	public function filter_tasks() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$filter = $_POST['zpm_filter'];
		$user_id = $_POST['zpm_user_id'];
		$tasks = array();

		if ($filter == '-1') {
			// All Tasks
			$tasks = Tasks::get_tasks();
		} elseif ($filter == '0') {
			// My Tasks
			$tasks = Tasks::get_user_tasks( $user_id );
		} elseif ($filter == '1') {
			// Completed Tasks 
			$tasks = Tasks::get_completed_tasks( 0 );
		} elseif ($filter == '2') {
			// Incompleted Tasks
			$tasks = Tasks::get_completed_tasks( 1 );
		} elseif ($filter == '3') {
		} 

		$html = '';

		$frontend = isset($_POST['frontend']) ? $_POST['frontend'] : false;
		
		foreach ($tasks as $task) {
			$new_row = Tasks::new_task_row($task);
			if (!$frontend) {
				$html .= $new_row;
			} else {
				$html .= '<a href="?action=task&id=' . $task->id . '">' . $new_row . '</a>';
			}
		}

		if (empty($tasks)) {
			$html = '<p class="zpm_error_message">' . __( 'No results found...', 'zephyr-project-manager' ) . '</p>';
		}

		$response = array(
			'html' => $html
		);

		echo json_encode($response);
		die();
	}

	/* Categories */
	// Creates a new category
	public function create_category() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array();
		$settings['name'] = (isset($_POST['category_name'])) ? sanitize_text_field( $_POST['category_name']) : '';
		$settings['description'] = (isset($_POST['category_description'])) ? sanitize_text_field( $_POST['category_description']) : '';
		$settings['color'] 	= (isset($_POST['category_color'])) ? sanitize_text_field( $_POST['category_color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$wpdb->insert( $table_name, $settings );
		Categories::display_category_list();
		die();
	}

	// Removes selected category
	public function remove_category() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array(
			'id' => $_POST['id']
		);

		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		Categories::display_category_list();
		die();
	}

	// Saves changes to the category
	public function update_category() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_CATEGORY_TABLE;
		$settings = array();
		$settings['name'] 			= (isset($_POST['category_name'])) ? sanitize_text_field( $_POST['category_name']) : '';
		$settings['description'] 	= (isset($_POST['category_description'])) ? sanitize_text_field( $_POST['category_description']) : '';
		$settings['color'] 	= (isset($_POST['category_color'])) ? sanitize_text_field( $_POST['category_color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$where = array(
			'id' => $_POST['category_id']
		);

		$wpdb->update( $table_name, $settings, $where );
		Categories::display_category_list();
		die();
	}

	// Displays all the categories
	public function display_category_list() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		Categories::display_category_list();
		die();
	}

	/**
	* Removes a comment/message from the database
	*/
	public function remove_comment() {
		global $wpdb;
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$table_name = ZPM_MESSAGES_TABLE;
		$comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '-1';
		$settings = array(
			'id' => $_POST['comment_id']
		);
		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		die();
	}
 	
 	/**
	* Returns the HTML for activities
	*/
	public function display_activities() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$all_activities = Activity::get_activities(array('offset' => $_POST['offset'] * 10, 'limit' => 10));
		echo Activity::display_activities($all_activities);
		die();
	}

	public function dismiss_notice() {
		check_ajax_referer( 'zpm_nonce', 'wp_nonce' );
		$notice_id = $_POST['notice'];
		if ($notice_id == 'review_notice') {
			update_option('zpm_review_notice_dismissed', '1');
		} else if ($notice_id == 'welcome_notice') {
			update_option('zpm_welcome_notice_dismissed', '1');
		} else {
			Utillities::dismiss_notice( $notice_id );
		}
	}

	public function update_user_access() {
		Utillities::update_user_access($_POST['user_id'], $_POST['access']);
		echo json_encode($_POST);
		die();
	}

	public function add_team() {
		$name = $_POST['name'];
		$description = $_POST['description'];
		$members = (array) $_POST['members'];
		$last_team = Members::add_team( $name, $description, $members );
		$team = Members::get_team( $last_team );

		$response = array(
			'html' => Members::team_single_html( Members::get_team( $last_team ) ),
			'team' => $team
		);
		echo json_encode( $response );
		die();
	}

	public function update_team() {
		$id = $_POST['id'];
		$name = $_POST['name'];
		$description = $_POST['description'];
		$members = (array) $_POST['members'];
		Members::update_team( $id, $name, $description, $members );
		echo json_encode( Members::team_single_html( Members::get_team( $id ) ) );
		die();
	}

	public function get_team() {
		$id = $_POST['id'];
		echo json_encode( Members::get_team( $id ) );
		die();
	}

	public function delete_team() {
		$id = $_POST['id'];
		Members::delete_team( $id );
		die();
	}
}