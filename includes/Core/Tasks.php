<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Zephyr;
use Inc\Core\Members;
use Inc\Base\BaseController;

class Tasks {
	/**
	* Retrieves a list of tasks
	* 	$args = [
	*      'limit'     => (string) The amount of tasks to retrieve
	*      'user_id'   => (string) The user ID to get the tasks for
	*      'project'   => (string) The project ID to get the tasks for
	*      'assignee'  => (string) The assignee to get the tasks for
	*      'completed' => (string) The completion status of the task
	*   ]
	* @return object
	*/
	public static function get_tasks( $args = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = array(
			'limit' 	=> false,
			'user_id'	=> false,
			'project'	=> false,
			'assignee' 	=> false,
			'completed' => 'all'
		);

		$args = wp_parse_args( $args, $defaults );
		$query = "SELECT * FROM $table_name WHERE ";
		if ($args['user_id']) {
			$query .= "user_id = '" . $args['user_id'] . "' AND ";
		}
		if ($args['project']) {
			$query .= "project = '" . $args['project'] . "' AND ";
		}
		if ($args['assignee']) {
			$query .= "assignee = '" . $args['assignee'] . "' AND ";
		}
		if ($args['completed'] !== 'all') {
			$query .= "completed = '" . $args['completed'] . "' AND ";
		}
		$query .= " parent_id = '-1' ORDER BY id DESC";
		if ($args['limit']) {
			$query .= " LIMIT " . $args['limit'] . " ";
		}
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the task data of a given task ID
	* @param int $task_id
	* @return object
	*/
	public static function get_task( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = '$task_id'";
		$task = $wpdb->get_row($query);
		return $task;
	}

	/**
	* Creates a new task
	*/
	public static function create( $data ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = [
			'user_id' => '-1',
			'parent_id' => '-1',
			'assignee' => '-1',
			'project' => '-1',
			'name' => '',
			'description' => '',
			'date_start' => '',
			'date_due' => '',
			'date_created' => date('Y-m-d H:i:s'),
			'date_completed' => '',
			'completed' => 0,
			'team' => ''
		];

		if (Zephyr::isPro()) {
			$defaults['custom_fields'] = '';
			if (isset($data['custom_fields'])) {
				$data['custom_fields'] = serialize( (array) $data['custom_fields'] );
			}
		}

		$args = wp_parse_args( $data, $defaults );
		$task = $wpdb->insert($table_name, $args);
		return $wpdb->insert_id;
	}

	/**
	* Creates a new task
	*/
	public static function copy( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$task = Tasks::get_task( $id );
		$args = [
			'user_id' => $task->user_id,
			'parent_id' => $task->parent_id,
			'assignee' => $task->assignee,
			'project' => $task->project,
			'name' => $task->name,
			'description' => $task->description,
			'date_start' => $task->date_start,
			'date_due' => $task->date_due,
			'date_created' => date('Y-m-d H:i:s'),
			'date_completed' => '',
			'completed' => 0
		];

		$task = $wpdb->insert($table_name, $args);
		return $wpdb->insert_id;
	}

	/**
	* Converts a taks to a project
	*/
	public static function convert( $id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$task = Tasks::get_task( $id );
		$subtasks = Tasks::get_subtasks( $id );
		$date = date('Y-m-d H:i:s');
		$user_id = get_current_user_id();
		$settings = [
			'user_id' 		 => $user_id,
			'name' 			 => $task->name,
			'description' 	 => $task->description,
			'completed' 	 => false,
			'date_start' 	 => $task->date_start,
			'date_due' 		 => $task->date_due,
			'date_completed' => ''
		];

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;

		if ( is_array( $subtasks ) ) {
			$tasks_table = ZPM_TASKS_TABLE;
			foreach ($subtasks as $subtask) {
				$task_settings = [
					'parent_id' 	 => '-1',
					'user_id' 		 => $user_id,
					'assignee' 		 => '-1',
					'project' 		 => $last_id,
					'name' 			 => $subtask->name,
					'description' 	 => '',
					'completed' 	 => false,
					'date_start' 	 => $date,
					'date_due' 		 => '',
					'date_created' 	 => $date,
					'date_completed' => ''
				];
				$wpdb->insert( $tasks_table, $task_settings );
			}
		}

		$new_project = Projects::get_project( $last_id );
		return $new_project;
	}

	// Updates a task
	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
	}

	/**
	* Creates a new task
	*/
	public static function delete( $id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$where = [
			'id' => $id
		];
		$wpdb->delete( $table_name, $where );
		return $id;
	}

	/**
	* Gets the subtasks for a task
	* @param int $task_id
	* @return object
	*/
	public static function get_subtasks( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE parent_id = '$task_id'";
		$subtasks = $wpdb->get_results($query);
		return $subtasks;
	}

	/**
	* Returns the total number of tasks
	* @return int
	*/
	public static function get_task_count() {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE parent_id = '-1'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Checks whether a task already exists
	* @param int $task_id
	* @return boolean
	*/
	public static function task_exists( $task_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = '$task_id'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Gets all tasks that are either complete or incomplete
	* @param boolean $completed
	* @return object
	*/
	public static function get_completed_tasks( $completed ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE completed = '$completed' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the number of completed tasks
	* @return int
	*/
	public static function get_completed_task_count() {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE completed = '1' AND parent_id = '-1'";
		$task_count = $wpdb->query($query);
		return $task_count;
	}

	/**
	* Gets all tasks assigned to given user
	* @param int $user_id
	* @return object
	*/
	public static function get_user_tasks( $user_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE assignee = '$user_id' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the completed tasks of a certain user
	* @param boolean $completed
	* @return object
	*/
	public static function get_user_completed_tasks( $user_id, $completed = '1' ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE assignee = '$user_id' AND completed = '$completed' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the tasks of a certain project
	* @param int $project_id
	* @return object
	*/
	public static function get_project_tasks( $project_id, $subtasks = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT * FROM $table_name WHERE project = '$project_id'";

		if($subtasks == null) {
			$query .= " AND parent_id = '-1'";
		} 
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Gets the number of tasks for a project
	* @param int $project_id
	* @return int
	*/
	public static function get_project_task_count( $project_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE project = '$project_id' AND parent_id = '-1'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Gets the number of completed tasks for a project
	* @param int $project_id
	* @return int
	*/
	public static function get_project_completed_tasks( $project_id ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id FROM $table_name WHERE project = '$project_id' AND completed = '1' AND parent_id = '-1'";
		$tasks = $wpdb->query($query);
		return $tasks;
	}

	/**
	* Retrieves all overdue tasks
	* @param int $project_id The ID of the project to filter by
	* @return array
	*/
	public static function get_overdue_tasks( $args = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$defaults = array(
			'project_id' => '-1',
			'assignee'	 => '-1'
		);
		$data = wp_parse_args( $args, $defaults );
		
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE ";
		if ($data['project_id'] !== '-1') {
			$query .= "project = '" . $data['project_id'] . "' AND ";
		}
		if ($data['assignee'] !== '-1') {
			$query .= "assignee = '" . $data['assignee'] . "' AND ";
		}
		$query .= "completed = '0' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		$date = new DateTime();
		$tasks_overdue = array();
		foreach ($tasks as $task) {
			if ($task->date_due == '0000-00-00 00:00:00') { 
				continue; 
			}

			$task_due = new DateTime($task->date_due);
			if ($task_due->format('m-d-Y') < $date->format('m-d-Y')) {
				array_push($tasks_overdue, $task);
			}
		}
		return $tasks_overdue;
	}

	/**
	* Returns a list of all tasks due this week
	* @param int $user_id
	* @param int $project_id
	* @return object
	*/
	public static function get_week_tasks( $assignee = null, $project_id = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, parent_id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE ";
		if (!is_null($project_id)) {
			$query .= "project_id = '$project_id' AND ";
		}
		if (!is_null($assignee)) {
			$query .= " assignee = '$assignee' AND ";
		}
		$query .= "parent_id = '-1' AND completed = '0' ORDER BY id DESC";
		$tasks = $wpdb->get_results($query);
		$datetime = new DateTime();
		$date = strtotime($datetime->format('d M Y'));
		$start_of_week = date("Y-m-d", strtotime('sunday last week'));  
		$end_of_week = date("Y-m-d", strtotime('sunday this week')); 
		$this_week_tasks = array();
		foreach ($tasks as $task) {
			$date_due = date("Y-m-d", strtotime($task->date_due)); 
			if ($date_due > $start_of_week && $date_due < $end_of_week) {
				array_push($this_week_tasks, $task);
			}
		}
		return $this_week_tasks;
	}

	/**
	* Returns the task creators name and creation date
	* @param int $project_id
	* @return string
	*/
	public static function task_created_by( $task_id ) {
		global $wpdb;

		$table_name = ZPM_TASKS_TABLE;

		$query = "SELECT user_id, date_created FROM $table_name WHERE id = $task_id";
		$data = $wpdb->get_row($query);
		$user = get_user_by('ID', $data->user_id);
		$today = new DateTime(date('Y-m-d H:i:s'));
		$created_on = new DateTime($data->date_created);
		$return = ($today->format('Y-m-d') == $created_on->format('Y-m-d')) 
					? sprintf( __( 'Created by %s at %s today', 'zephyr-project-manager' ), $user->display_name, $created_on->format('H:i') )
					: sprintf( __( 'Created by %s on %s at %s', 'zephyr-project-manager' ), $user->display_name, $created_on->format('d M'), $created_on->format('H:i') );
		return $return;
	}

	/**
	* Gets all the tasks 
	* @param int $project_id (optional)
	* @return object
	*/
	public function get_task_list( $project_id = null ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$query = "SELECT id, user_id, project, assignee, name, description, categories, completed, date_created, date_start, date_due, date_completed FROM $table_name WHERE project = '$project_id' AND parent_id = '-1'";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Retrieves all the comments for a task 
	* @param int $task_id The ID of the task to retrieve the comments for
	* @return object
	*/
	public static function get_comments( $task_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task' AND subject_id = '$task_id' ORDER BY date_created DESC";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Returns the data for a specific comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'task' AND id = '$comment_id'";
		$comment = $wpdb->get_row($query);
		return $comment;
	}

	/**
	* Retrieves all the attachments for a single comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment_attachments( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'task' AND parent_id = '$comment_id' ORDER BY date_created DESC";
		$attachments = $wpdb->get_results($query);
		return $attachments;
	}

	/**
	* Gets all the attachments for all tasks 
	* @return array
	*/
	public static function get_attachments() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'task'";
		$attachments = $wpdb->get_results($query);
		$attachments_array = [];

		foreach($attachments as $attachment) {
			if (unserialize($attachment->type) == 'attachment') {
				$attachments_array[] = array(
					'id' 	  => $attachment->id,
					'user_id' => $attachment->user_id,
					'subject' => $attachment->subject,
					'subject_id' => $attachment->subject_id,
					'message' => unserialize($attachment->message),
					'date_created' => $attachment->date_created
				);
			}
		}

		return $attachments_array;
	}

	/**
	* Displays the new task modal
	*/
	public static function new_task_modal() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/new_task.php' );
	}

	/**
	* Gets the html for the task list page
	* @param array $filters
	*/
	public static function view_task_list( $filters = NULL ) {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/task-list.php' );
	}

	/**
	* Includes the task view modal container
	* @param int $task_id The ID of the modal to display
	*/
	public static function view_container( $task_id = null) {
		?>
		<div id="zpm_task_view_container" class="zpm-modal" data-task-id="<?php echo $task_id; ?>">
		</div>
		<?php
	}

	/**
	* Includes the task view modal
	* @param int $task_id The ID of the modal to display
	*/
	public static function view_task_modal( $task_id ) {
		include (ZPM_PLUGIN_PATH . '/templates/parts/task_view.php');
	}

	/**
	* Generates the HTML for a new task row
	* @param object $task The data for the task
	* @return HTML
	*/
	public static function new_task_row( $task ) {
		$Projects = new Projects();
		$Task = new Tasks();
		$today = new DateTime();
		$due_datetime = new DateTime($task->date_due);
		$users = get_users();
		$user_id = wp_get_current_user()->ID;
		$task_project = Projects::get_project($task->project);
		$project_name = is_object($task_project) ? $task_project->name : '';
        $row_classes = (($task->completed == '1') ? 'zpm_task_complete' : '');
        $assignee_details = BaseController::get_project_manager_user($task->assignee);
		$due_today = ($today->format('Y-m-d') == $due_datetime->format('Y-m-d')) ? true : false;
		$overdue = ($today > $due_datetime && !$due_today) ? true : false;
		$due_date = (!$due_today) ? $due_datetime->format('M d') : 'Today';
		$due_date = ($task->date_due !== '0000-00-00 00:00:00') ? $due_date : '';
		$complete = (($task->completed == '1') ? 'completed disabled' : '');
		$task = Tasks::get_task( $task->id );
		$checked = (($task->completed == '1') ? 'checked' : '');
        ob_start(); ?>

        <div class="zpm_task_list_row <?php echo $row_classes; ?>" data-task-id="<?php echo $task->id; ?>" data-task-name="<?php echo $task->name; ?>">
			<label for="zpm_task_id_<?php echo $task->id; ?>" class="zpm_checkbox_label">
				<input type="checkbox" id="zpm_task_id_<?php echo $task->id; ?>" name="zpm_task_id_<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $checked; ?> data-task-id="<?php echo $task->id; ?>">
				<div class="zpm_main_checkbox">
					<svg width="20px" height="20px" viewBox="0 0 20 20">
						<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
						<polyline points="4 11 8 15 16 6"></polyline>
					</svg>
				</div>
		    </label>

			<span class="zpm_task_list_data task_name">
				<?php echo $task->name; ?>
				<?php if ($task->description !== '' && $task->description !== null) : ?>
					<span class="zpm_task_description"> - <?php echo stripslashes($task->description); ?></span>
				<?php endif; ?>
			</span>

			<span class="zpm_task_details">
				<?php if ($project_name !== '') : ?>
					<span title="Project Team" class="zpm_task_project"><?php echo $project_name; ?></span>
				<?php endif; ?>

				<?php if ($task->team !== "") : ?>
					<?php $team = Members::get_team( $task->team ); ?>
					<?php if ($team['name'] !== "" && !empty($team['name'])) : ?>
						<span title="Team" class="zpm_task_project zpm-task-team"><?php echo $team['name']; ?></span>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (!empty($assignee_details)) : ?> 
					<span title="Assignee" class='zpm_task_assignee' style='background-image: url("<?php echo $assignee_details['avatar'] ?>"); <?php echo $assignee_details['avatar'] == '' ? 'display: none;' : ''; ?>' title="<?php echo $assignee_details['name'] ?>"></span>
				<?php endif; ?>
				<span class="zpm_task_due_date <?php echo $overdue ? 'zpm_overdue' : ''; ?>"><?php echo $due_date; ?></span>
			</span>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	* Generates the HTML for a new single comment
	*
	* @param object $comment The comment data for the comment to add
	* @return HTML
	*/
	public static function new_comment( $comment ) {
		$current_user = wp_get_current_user();
		$this_user = BaseController::get_project_manager_user($comment->user_id);
		$datetime1 = new DateTime(date('Y-m-d H:i:s'));
		$datetime2 = new DateTime($comment->date_created);

		if ($datetime1->format('m-d') == $datetime2->format('m-d')) {
			// Was sent today
			$time_sent = $datetime2->format('H:i');
		} else {
			// Was sent earlier than today
			$time_sent = $datetime2->format('H:i m/d');
		}
				
		$timediff = human_time_diff(date_timestamp_get($datetime1), date_timestamp_get($datetime2));
		$comment_attachments = Tasks::get_comment_attachments($comment->id);

		$new_comment = '';

		if (unserialize($comment->type) !== 'attachment') {

			$new_comment .= '<div data-zpm-comment-id="' . $comment->id . '" class="zpm_comment">
				<span class="zpm_comment_user_image">
					<span class="zpm_comment_user_avatar" style="background-image: url(' . $this_user['avatar'] . ')"></span>
				</span>';

			if ($comment->user_id == $current_user->ID) {
				$new_comment .= '<span class="zpm_delete_comment lnr lnr-trash"></span>';
			}
				
			$new_comment .= '<span class="zpm_comment_user_text">
				<span class="zpm_comment_from">' . $this_user['name'] . '</span>
				<span class="zpm_comment_time_diff">' . $time_sent . '</span>
				<p class="zpm_comment_content">'. stripslashes_deep(unserialize($comment->message)) . '</p>';

			if (!empty($comment_attachments)) {
				$new_comment .= '<ul class="zpm_comment_attachments"><p>Attachments:</p>';

				foreach($comment_attachments as $attachment) {
					$attachment_id = unserialize( $attachment->message );
					$attachment = wp_get_attachment_url( $attachment_id );
					if (wp_attachment_is_image( $attachment_id )) {
						// Image preview
						$new_comment .= '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download><img class="zpm-image-attachment-preview" src="' . $attachment . '"></a></li>';
					} else {
						// Attachment link
						$new_comment .= '<li class="zpm_comment_attachment"><a class="zpm_link" href="' . $attachment . '" download>' . $attachment . '</a></li>';
					}
				}
				$new_comment .= '</ul>';
			}
			$new_comment .= '</span></div>';
		}
		return $new_comment;
	}

	public static function search( $query ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$results = $wpdb->get_results($wpdb->prepare(
		    "SELECT
		        id, name
		    FROM
		        `{$table_name}`
		    WHERE
		        name LIKE %s LIMIT 10;",
		    '%' . $wpdb->esc_like($query) . '%'
		));
		return $results;
	}

	public static function complete( $id, $complete ) {
		global $wpdb;
		$table_name = ZPM_TASKS_TABLE;
		$date = date('Y-m-d H:i:s');

		$settings = array(
			'completed' 	 => $complete,
			'date_completed' => $date,
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	public static function get_templates() {
		$templates = get_option('zpm_task_templates', array());
		return maybe_unserialize( $templates );
	}

	public static function get_template( $id ) {
		$templates = Tasks::get_templates();

		foreach ( $templates as $template ) {
			if ($template['id'] == $id) {
				return $template;
			}
		}
		return null;
	}

	public static function create_template( $name, $custom_fields ) {
		$templates = Tasks::get_templates();

		$last_template = end( $templates );
		$id = !empty( $last_template ) ? (int) $last_template['id'] + 1 : '0';

		$new_template = array(
			'id' => $id,
			'name' => $name,
			'custom_fields' => (array) $custom_fields
		);

		reset( $templates );

		$templates[] = $new_template;
		update_option( 'zpm_task_templates', serialize( $templates ) );

		return $new_template['id'];
	}

	public static function remove_template( $id ) {
		$templates = Tasks::get_templates();

		foreach ($templates as $key => $value) {
			if ($value['id'] == $id) {
				unset( $templates[$key] );
			}
		}

		update_option( 'zpm_task_templates', serialize( $templates ) );

		return true;
	}

	public static function update_template( $id, $name, $fields ) {
		$templates = Tasks::get_templates();

		foreach ($templates as $key => $value) {
			if ($value['id'] == $id) {
				$templates[$key]['name'] = $name;
				$templates[$key]['custom_fields'] = (array) $fields;
			}
		}

		update_option( 'zpm_task_templates', serialize( $templates ) );

		return true;
	}

	public static function template_row_html( $id ) {
		$template = Tasks::get_template( $id );
		$is_default = ( $id == Tasks::get_default_template() ) ? true : false;

		ob_start();

		?>
		<div class="zpm-custom-task-template" data-template-id="<?php echo $template['id']; ?>">

			<span class="zpm-task-template-name"><?php echo $template['name']; ?> <?php echo Tasks::get_default_template() == $template['id'] ? '<span class="zpm-task-template-default-notice">' . __( 'Default', 'zephyr-project-manager' ) . '</span>' : ''; ?></span>

			<label for="zpm-task-template-checkbox-<?php echo $template['id']; ?>" class="zpm_checkbox_label">

				<input type="checkbox" id="zpm-task-template-checkbox-<?php echo $template['id']; ?>" name="zpm_can_zephyr" class="zpm-default-task-template zpm_toggle invisible" value="1" data-template-id="<?php echo $template['id']; ?>" <?php echo $is_default ? 'checked' : ''; ?>>

				<div class="zpm_main_checkbox">
					<svg width="20px" height="20px" viewBox="0 0 20 20">
						<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
						<polyline points="4 11 8 15 16 6"></polyline>
					</svg>
				</div>
		    </label>

		    <span class="zpm-remove-task-template lnr lnr-cross" data-template-id="<?php echo $template['id']; ?>"></span>
		</div>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function get_default_template() {
		$default = get_option( 'zpm_default_template', 0 );
		return $default;
	}

	public static function set_default_template( $id ) {
		update_option( 'zpm_default_template', $id );
	}

	public static function send_comment( $task_id, $data, $files = null ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');

		$user_id = isset($data['user_id']) ? sanitize_text_field( $data['user_id']) : $this->get_user_id();
		$subject_id = isset($data['subject_id']) ? sanitize_text_field( $data['subject_id']) : '';
		$message = isset($data['message']) ? serialize( sanitize_textarea_field($data['message']) ) : '';
		$type = isset($data['type']) ? serialize( $data['type']) : '';
		$parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
		$subject = isset($data['subject']) ? $data['subject'] : '';

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $task_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$wpdb->insert($table_name, $settings);
		return $wpdb->insert_id;
	}
	
}