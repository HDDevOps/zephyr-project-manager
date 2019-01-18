<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Api\Emails;
use Inc\Core\Tasks;
use Inc\Core\Activity;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;

class Projects extends Tasks {
	function __construct() {
		// Update project progress daily
		add_action( 'zpm_update_progress', array($this, 'update_progress') );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'daily';
		$hook = 'zpm_update_progress';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}

		// Send weekly email progress reports
		add_action( 'zpm_weekly_updates', array($this, 'weekly_updates') );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'weekly';
		$hook = 'zpm_weekly_updates';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}

		// Send daily updates on due tasks
		add_action( 'zpm_task_notifications', array($this, 'task_notifications') );
		date_default_timezone_set('UTC');
		$time = strtotime('00:00:00');
		$recurrence = 'daily';
		$hook = 'zpm_task_notifications';
		if ( !wp_next_scheduled( $hook ) ) {
			wp_schedule_event( $time, $recurrence, $hook);
		}
	}

	/**
	* Creates a new project
	* @param array $args Array containing the project details
	* 	$args = [
	*      'user_id'  	    => (int) ID of the project creator
	*      'name' 	  	    => (string) Name of the project
	*      'description'    => (string) Description for the project
	*      'team'    	    => (string) Team assigned to the project
	*      'categories'     => (string) Categories assigned to the project
	*      'completed'      => (bool) Completion status of the project
	*      'date_start'     => (string) Datetime that the project is scheduled to start 
	*      'date_due'       => (string) Datetime that the project is due
	*      'date_created'   => (string) Datetime that the project was created
	*      'date_completed' => (string) Datetime that the project was completed
	*   ]
	* @return int Returns the ID of the newly created project project
	*/
	public static function new_project( $args = null ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$defaults = array(
			'user_id'        => get_current_user_id(),
			'name'           => 'Untitled Project',
			'description'    => '',
			'team'           => '',
			'categories'     => '',
			'completed'      => '1',
			'date_start'     => date('Y-m-d H:i:s'),
			'date_due'       => '',
			'date_created'   => date('Y-m-d H:i:s'),
			'date_completed' => '',
		);
		$data = wp_parse_args( $args, $defaults );
		$wpdb->insert( $table_name, $data );
		$new_project_id = $wpdb->insert_id;
		Activity::log_activity($data['user_id'], $wpdb->insert_id, '', $data['name'], 'project', 'project_added', $data['date_created']);
		return $new_project_id;
	}

	/**
	* Retrieves all projects
	* @param int $limit The amount of projects to retrieve
	* @return object
	*/
	public static function get_projects( $limit = null ) {
		global $wpdb;
		$defaults = array(
			'limit' => '-1'
		);
		$table_name = ZPM_PROJECTS_TABLE;
		if (!is_null($limit)) {
			$query = "SELECT id, user_id, name, description, completed, team, categories, status, date_created, date_start, date_due, date_completed, other_data FROM $table_name LIMIT $limit ORDER BY id DESC";
		} else {
			$query = "SELECT id, user_id, name, description, completed, team, categories, status, date_created, date_start, date_due, date_completed, other_data FROM $table_name ORDER BY id DESC";
		}
		$projects = $wpdb->get_results($query);

		foreach ($projects as $project) {
			$project->status = $project->status == "" ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		}
		
		return $projects;
	}

	/**
	* Retrieves the project data for a single project
	* @param int $project_id The id of the project to retrieve the data for
	* @return object
	*/
	public static function get_project( $project_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT id, user_id, name, description, completed, team, categories, status, date_created, date_start, date_due, date_completed, other_data, type FROM $table_name WHERE id = $project_id";
		$project = $wpdb->get_row($query);
		if (is_object($project)) {
			$project->status = $project->status == "" ? maybe_unserialize( $project->status ) : array();
			$project->team = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
		}
		return $project;
	}


	/**
	* Gets the total number of projects
	* @return int $id The project ID
	*/
	public static function delete_project( $id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$tasks_table = ZPM_TASKS_TABLE;
		$project_name = Projects::get_project( $id );
		$project_name = $project_name->name;
		$settings = array( 'id' => $id );
		$wpdb->delete( $table_name, $settings, [ '%d' ] );
		$tasks = Tasks::get_project_tasks( $id );

		foreach ($tasks as $task) {
			$settings = array(
				'id' => $task->id
			);
			$wpdb->delete( $tasks_table, $settings, [ '%d' ] );
		}

		$date_deleted = date('Y-m-d H:i:s');
		$subject_name = $project_name;

		Activity::log_activity( get_current_user_id(), $id, '', $subject_name, 'project', 'project_deleted', $date_deleted );
	}

	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
	}

	/**
	* Gets the total number of projects
	* @return int
	*/
	public static function project_count() {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT id FROM $table_name";
		$project_count = $wpdb->query($query);
		return $project_count;
	}

	/**
	* Retrieves the completed project count
	* @return int
	*/
	public static function completed_project_count() {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT id FROM $table_name WHERE completed = '1'";
		$project = $wpdb->query($query);
		return $project;
	}

	/**
	* Gets the percentage of completion of a project
	* @param int $id The ID of the Project to return the percentage for
	* @return int The percent complete without the % symbol
	*/
	public static function percent_complete( $project_id ) {
		$total_tasks = Tasks::get_project_task_count( $project_id );
		$completed_tasks = Tasks::get_project_completed_tasks( $project_id );
		$percent_complete = ($total_tasks !== 0) ? floor($completed_tasks / $total_tasks * 100) : 100;
		return $percent_complete;
	}

	/**
	* Retrieves all projects created by a specific user
	* @param int $user_id The ID of the user to retrieve the data for
	* @return object
	*/
	public function get_user_projects( $user_id ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$query = "SELECT id, user_id, name, description, completed, team, categories, status, date_created, date_start, date_due, date_completed, other_data FROM $table_name WHERE user_id = '" . $user_id . "'";
		$projects = $wpdb->get_results($query);
		return $projects;
	}

	/**
	* Returns the byline with the project creators name and the date it was craeted
	* Example: Project Name created by Dylan on 24-07-2018 at 22:00
	* @param int $project_id The project ID to create the byline for
	* @return string
	*/
	public static function project_created_by( $project_id ) {
		global $wpdb;

		$table_name = ZPM_PROJECTS_TABLE;

		$query = "SELECT user_id, date_created FROM $table_name WHERE id = $project_id";
		$data = $wpdb->get_row($query);
		$user = get_user_by('ID', $data->user_id);
		$today = new DateTime(date('Y-m-d H:i:s'));
		$created_on = new DateTime($data->date_created);
		
		$return = ($today->format('Y-m-d') == $created_on->format('Y-m-d')) 
					? 'Created by ' . $user->display_name . ' at ' . $created_on->format('H:i')
					: 'Created by ' . $user->display_name . ' on ' . $created_on->format('d M') . ' at ' . $created_on->format('H:i');
		return $return;
	}

	/**
	* Generates the HTML for a new project cell
	* @param object $project The Project data to create the new project cell for
	* @return string
	*/
	public static function new_project_cell( $project ) {
		$Tasks = new Tasks();
		$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_projects') );
		$color = unserialize( $project->other_data)['color'];
		$complete = ( ($project->completed == '1') ? 'completed disabled' : '' );
		$categories = maybe_unserialize( $project->categories );
		$team = maybe_unserialize( $project->team );
		$total_tasks = $Tasks->get_project_task_count( $project->id );
		$completed_tasks = $Tasks->get_project_completed_tasks( $project->id );
		$active_tasks = (int) $total_tasks - (int) $completed_tasks;
		$message_count = sizeof( Projects::get_comments( $project->id ) );

		ob_start();
		?>
		<div class="zpm_project_grid_cell">
			<div class="zpm_project_grid_row zpm_project_item" data-project-id="<?php echo $project->id; ?>">
				<a href="<?php echo $base_url; ?>&action=edit_project&project=<?php echo $project->id; ?>" data-project_id="<?php echo $project->id; ?>" class="zpm_project_title project_name">
					<span class="zpm_project_grid_name"><?php echo $project->name; ?></span>
					<!-- Project options button and dropwdown -->
					<span class="zpm_project_grid_options">
						<i class="zpm_project_grid_options_icon dashicons dashicons-menu"></i>
						<div class="zpm_dropdown_menu">
							<ul class="zpm_dropdown_list">
								<li id="zpm_delete_project">Delete Project</li>
								<li id="zpm_copy_project">Copy Project</li>
								<li id="zpm_export_project" class="zpm_dropdown_subdropdown">Export Project
									<div class="zpm_export_dropdown zpm_submenu_item">
										<ul>
											<li id="zpm_export_project_to_csv" class="zpm_project_option_sub">Export to CSV</li>
											<li id="zpm_export_project_to_json" class="zpm_project_option_sub">Export to JSON</li>
										</ul>
									</div>
								</li>
								<li id="zpm_add_project_to_dashboard">Add to Dashboard</li>
							</ul>
						</div>
					</span>
				</a>

				<div class="zpm_project_body">
					<span class="zpm_project_description project_description"><?php echo $project->description; ?></span>
					<div id="zpm_project_progress">
						<span class="zpm_project_stat">
							<p class="zpm_stat_number"><?php echo $completed_tasks; ?></p>
							<p>Completed Tasks</p>
						</span>
						<span class="zpm_project_stat">
							<p class="zpm_stat_number"><?php echo $active_tasks; ?></p>
							<p>Active Tasks</p>
						</span>
						<span class="zpm_project_stat">
							<p class="zpm_stat_number"><?php echo $message_count; ?></p>
							<p>Messages</p>
						</span>
					</div>
					<div class="zpm_project_progress_bar_background">
						<div class="zpm_project_progress_bar" data-total_tasks="<?php echo $total_tasks; ?>" data-completed_tasks="<?php echo $completed_tasks; ?>"></div>
					</div>
					
					<div class="zpm_project_categories">

						<?php
						if (is_array($categories)) :
							foreach($categories as $category) :
								$category_data = Categories::get_category($category);
								?>
								<span class="zpm_task_project_label" style="background-color: <?php echo $category_data->color; ?>"><?php echo $category_data->name; ?></span>
							<?php endforeach;
						endif; 
						?>							
							
					</div>

					<?php
					$i = 0;
					if (sizeof((array)$team) !== 0) : ?>
						<div class="zpm_project_grid_member">
							<div class="zpm_project_avatar">
								<?php
								foreach ( (array) $team as $member ) :
									$member = BaseController::get_project_manager_user($member);
									if (!isset($member['name'])) : ?>
										<p class="zpm_friendly_notice">There are no members assigned to this project.</p>
										<?php continue; ?>
									<?php endif; ?>

									<span class="zpm_avatar_container">
										<span class="zpm_avatar_background"></span>
										<span class="zpm_avatar_image" title="<?php echo $member['name']; ?>" style="background-image: url(<?php echo $member['avatar']; ?>);">
										</span>
									</span>
									
								<?php
								$i++;
								endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	* Returns the HTML for a project item
	*/
	public function frontend_project_item( $project ) {
		ob_start();
		?>
		<li class="zpm-project-item col-md-12" data-project-id="<?php echo $project->id; ?>">
			<a href="?action=project&id=<?php echo $project->id; ?>"><?php echo $project->name; ?></a>
		</li>
		<?php
		return ob_get_clean();
	}

	/**
	* Renders a <select> input field with all the projects
	* @param string $id The ID for the input field
	* @param int $default The ID of the project that should be selected by default
	* @return string
	*/
	public static function project_select( $id = null, $default = null ) {
		$projects = Projects::get_projects();
		$html = !is_null($id) ? '<select id="' . $id . '" class="zpm_input">' : '<select class="zpm_input">';
		$html .= '<option>None</option>';
		foreach ($projects as $project) {
			if (!is_null($default) && $default == $project->id) {
				$html .= '<option value="' . $project->id . '" selected>' . $project->name . '</option>';
			} else {
				$html .= '<option value="' . $project->id . '">' . $project->name . '</option>';
			}
		}
		$html .= '</select>';

		if (empty($projects)) {
			$html = '<p class="zpm_error">There are no projects yet.</p>';
		}

		echo $html;
	}

	public function update_project_status($id, $status, $color) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$settings = array(
			'status' => serialize(array(
				'status' => $status,
				'color' => $color
			))
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	public function update_members($id, $members) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$settings = array(
			'team' => serialize($members)
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	/**
	* Marks a task as complete
	*/
	public function mark_complete( $id, $complete ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;

		$settings = array(
			'completed' => $complete
		);

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $settings, $where );
	}

	/**
	* Updates the progress of a project
	*/
	public function update_progress( $id = null ) {
		$chart_data = array();
		$current_chart_data = get_option('zpm_chart_data');

		if ($id) {
			if ($id == '' || $id == '-1') {
				return;
			}
			$project = Projects::get_project( $id );
			$data = isset($current_chart_data[$project->id]) ? $current_chart_data[$project->id] : array();
			$task_count = Tasks::get_project_task_count($project->id);
			$completed_tasks = Tasks::get_project_completed_tasks($project->id);
			$pending_tasks = $task_count - $completed_tasks;
			$args = array( 'project_id' => $project->id );
			$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));

			$project_data = array(
				'project'			=> $project->id,
				'tasks' 			=> $task_count,
				'completed_tasks' 	=> $completed_tasks,
				'pending_tasks' 	=> $pending_tasks,
				'overdue_tasks' 	=> $overdue_tasks,
				'date'				=> date('d M')
			);

			$added = false;

			foreach ($data as $key => $value) {
				if (!$added) {
					if ($data[$key]['date'] == $project_data[$date]) {
						$data[$key] = $project_data;
						$added = true;
					}
				}
			}

			if (!$added) {
				array_push($data, $project_data);
			}
			
			$chart_data[$project->id] = $data;
		} else {
			$all_projects = Projects::get_projects();
			foreach ($all_projects as $project) {
				$data = isset($current_chart_data[$project->id]) ? $current_chart_data[$project->id] : array();
				
				$task_count = Tasks::get_project_task_count($project->id);
				$completed_tasks = Tasks::get_project_completed_tasks($project->id);
				$pending_tasks = $task_count - $completed_tasks;
				$args = array( 'project_id' => $project->id );
				$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
				$project_data = array(
					'project'			=> $project->id,
					'tasks' 			=> $task_count,
					'completed_tasks' 	=> $completed_tasks,
					'pending_tasks' 	=> $pending_tasks,
					'overdue_tasks' 	=> $overdue_tasks,
					'date'				=> date('d M')
				);

				$added = false;

				foreach ($data as $key => $value) {
					if (!$added) {
						if ($data[$key]['date'] == $project_data[$date]) {
							$data[$key] = $project_data;
							$added = true;
						}
					}
				}

				if (!$added) {
					array_push($data, $project_data);
				}

				
				$chart_data[$project->id] = $data;
			}
		}

		update_option('zpm_chart_data', $chart_data);
	}

	/**
	* Retrieves all the comments for a project 
	* @param int $task_id The ID of the task to retrieve the comments for
	* @return object
	*/
	public static function get_comments( $project_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'project' AND subject_id = '$project_id' ORDER BY date_created DESC";
		$tasks = $wpdb->get_results($query);
		return $tasks;
	}

	/**
	* Returns the data for a specific project comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'project' AND id = '$comment_id'";
		$comment = $wpdb->get_row($query);
		return $comment;
	}

	/**
	* Retrieves all the attachments for a single project comment 
	* @param int $comment_id
	* @return object
	*/
	public static function get_comment_attachments( $comment_id ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject, subject_id, message, type, date_created FROM $table_name WHERE subject = 'project' AND parent_id = '$comment_id' ORDER BY date_created DESC";
		$attachments = $wpdb->get_results($query);
		return $attachments;
	}

	/**
	* Gets all the attachments for all projects 
	* @return array
	*/
	public static function get_attachments() {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$query = "SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE subject = 'project'";
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
		$comment_attachments = Projects::get_comment_attachments($comment->id);

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

	public static function file_html( $attachment_id, $comment_id ) {
		$attachment = BaseController::get_attachment( $comment_id );
		$project_id = $attachment->subject_id;
		$attachment_datetime = new DateTime();
		$attachment_date = $attachment_datetime->format('d M Y H:i');
		$attachment_url = wp_get_attachment_url($attachment_id);
		$attachment_type = wp_check_filetype($attachment_url)['ext']; 
		$attachment_name = basename(get_attached_file($attachment_id));
		ob_start();
	?>
	<div class="zpm_file_item_container" data-project-id="<?php echo $project_id; ?>">
		<div class="zpm_file_item" data-attachment-id="<?php echo $attachment_id; ?>" data-attachment-url="<?php echo $attachment_url; ?>" data-attachment-name="<?php echo $attachment_name; ?>" data-task-name="None" data-attachment-date="<?php echo $attachment_date; ?>">
			<?php if (wp_attachment_is_image($attachment_id)) : ?>
				<!-- If attachment is an image -->
				<div class="zpm_file_preview" data-zpm-action="show_info">
					<span class="zpm_file_image" style="background-image: url(<?php echo $attachment_url; ?>);"></span>
				</div>
			<?php else: ?>
				<div class="zpm_file_preview" data-zpm-action="show_info">
					<div class="zpm_file_type"><?php echo '.' . $attachment_type; ?></div>
				</div>
			<?php endif; ?>

			<h4 class="zpm_file_name">
				<?php echo $attachment_name; ?>
				<span class="zpm_file_actions">
					<span class="zpm_file_action lnr lnr-download" data-zpm-action="download_file"></span>
					<span class="zpm_file_action lnr lnr-question-circle" data-zpm-action="show_info"></span>
					<span class="zpm_file_action lnr lnr-trash" data-zpm-action="remove_file"></span>
				</span>
			</h4>
		</div>
	</div>
	<?php
	return ob_get_clean();
	}

	/**
	* Displays HTML for the New Project Modal
	*/
	public static function project_modal() {
		?>
		<div id="zpm_project_modal" class="zpm-modal">
			<div class="zpm_modal_body">
				<h2>New Project</h2>
				<h3>Create a new project</h3><span class="zpm_close_modal">+</span>
				<input class="zpm_project_name_input" name="zpm_project_name" placeholder="Add a project name" />
				<div class="zpm_modal_content">
					<div class="zpm_col_container">
						
						<?php ob_start(); ?>

						<div class="zpm_modal_item">
							<div class="image zpm_project_selected" data-project-type="list">
							<img class="zpm_selected_image" src="<?php echo ZPM_PLUGIN_URL . "/assets/img/project_list_selected.png"; ?>" />
							<img src="<?php echo ZPM_PLUGIN_URL . "/assets/img/project_list.png"; ?>" />
							</div>
							<h4 class="title">List</h4>
							<p class="description">Organize your work in an itemized list.</p>
						</div>

						<?php
							$project_types = ob_get_clean();
							echo apply_filters( 'zpm_project_types', $project_types );
						?>
					</div>
				</div>

				<input id="zpm-project-type" type="hidden" value="list"> 
				<div class="zpm_modal_buttons">
					<button id="zpm_modal_add_project" class="button zpm_button">Create Project</button>

					<?php if (!BaseController::is_pro()) : ?>
						<p class="zpm-pro-upselling">Create Kanban style board projects with the <a class="zpm-pro-link" href="https://zephyr-one.com/purchase-pro" target="_blank">Pro version</a>.</p>
					<?php endif; ?>
				</div>
			</div>
		</div
		<?php
	}

	/**
	* Copy a project
	* @param array $args [
	*	'project_id'   => (int) ID of the project to copy
	*	'project_name' => (string) The new name of the copied project
	*	'copy_options' => (array) Options to copy
	* ]
	*/
	public static function copy_project( $args = null ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
		$defaults = [
			'project_id' => -1,
			'project_name' => false,
			'copy_options' => array()
		];
		$args = wp_parse_args( $args, $defaults );
		$project = Projects::get_project($args['project_id']);
		$description = in_array('description', $args['copy_options']) ? $project->description : '';
		$date = date('Y-m-d H:i:s');
		$date_start = in_array('start_date', $args['copy_options']) ? $project->date_start : $date;
		$date_due = in_array('due_date', $args['copy_options']) ? $project->date_due : '';

		$settings = array(
			'user_id' 	  	 => wp_get_current_user()->ID,
			'name' 		  	 => $args['project_name'],
			'description' 	 => $description,
			'completed'   	 => $project->completed,
			'categories'	 => $project->categories,
			'date_start'  	 => $date_start,
			'date_due' 	  	 => $date_due,
			'date_created' 	 => $date,
			'other_data'	 => $project->other_data,
			'date_completed' => ''
		);

		$wpdb->insert( $table_name, $settings );
		$last_id = $wpdb->insert_id;
		$last_project = Projects::get_project($last_id);
		$tasks = Tasks::get_project_tasks($args['project_id']);
		$task_table = ZPM_TASKS_TABLE;

		$i = $j = 0;
		if ((in_array('tasks', $args['copy_options']))) {
			foreach ($tasks as $task) {
				$settings = (array) $task;
				unset($settings['id']);
				$settings['project'] = $last_id;
				$wpdb->insert( $task_table, $settings );
				$last_task_id = $wpdb->insert_id;
				$i++;
				if ($settings['completed']) {
					$j++;
				}

				$subtasks = Tasks::get_subtasks($task->id);

				foreach ($subtasks as $subtask) {
					$settings = array(
						'parent_id'		 => $last_task_id,
						'user_id' 		 => $subtask->user_id,
						'assignee' 		 => $subtask->assignee,
						'project' 		 => $last_id,
						'name' 			 => $subtask->name,
						'completed' 	 => $subtask->completed,
						'date_start' 	 => $subtask->date_start,
						'date_due' 		 => $subtask->date_due,
						'date_created' 	 => $subtask->date_created,
						'date_completed' => ''
					);

					$wpdb->insert( $task_table, $settings );
				}
			}
		}

		$last_project->task_count = $i;
		$last_project->completed_tasks = $j;
		return $last_project;
	}

	/**
	* Adds a projoct to the Dashboard projects
	* @param int $project_id The ID of the project to add to the Dashboard
	*/
	public static function add_to_dashboard( $project_id ) {
		$option = maybe_unserialize(get_option('zpm_dashboard_projects', array()));
		if (!in_array($project_id, $option)) {
			$option[] = $project_id;
		}
		update_option('zpm_dashboard_projects', serialize($option));
	}

	/**
	* Gets all the Dashboard projects
	* @return array
	*/
	public static function get_dashboard_projects() {
		$option = maybe_unserialize( get_option('zpm_dashboard_projects', array()) );
		return $option;
	}

	/**
	* Removes a project from the Dashboard
	* @param int $project_id The ID of the project to remove from the Dashboard
	*/
	public static function remove_from_dashboard( $project_id ) {
		$dashboard_projects = Projects::get_dashboard_projects();
		if (($project_id = array_search($project_id, $dashboard_projects)) !== false) {
		    unset($dashboard_projects[$project_id]);
		}
		update_option('zpm_dashboard_projects', serialize($dashboard_projects));
	}

	/**
	* Sends weekly email updates on project progress
	*/
	public function weekly_updates() {
		$projects = Projects::get_projects();
		//$progress_data = get_option('zpm_chart_data');
		Emails::weekly_updates($projects);
	}

	/**
	* Sends daily notifications via email for due tasks
	*/
	public function task_notifications() {
		$tasks = Tasks::get_week_tasks();
		Emails::task_notifications( $tasks );
	}

	/**
	* Search for projects
	*/
	public static function search( $query ) {
		global $wpdb;
		$table_name = ZPM_PROJECTS_TABLE;
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

	public static function send_comment( $project_id, $data, $files = null ) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$date =  date('Y-m-d H:i:s');

		$user_id = isset($data['user_id']) ? sanitize_text_field( $data['user_id']) : $this->get_user_id();
		$message = isset($data['message']) ? serialize( sanitize_textarea_field($data['message']) ) : '';
		$type = isset($data['type']) ? serialize( $data['type']) : '';
		$parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
		$subject = 'project';

		$settings = array(
			'user_id' => $user_id,
			'subject' => $subject,
			'subject_id' => $project_id,
			'message' => $message,
			'date_created' => $date,
			'type' => $type,
			'parent_id' => $parent_id,
		);

		$wpdb->insert($table_name, $settings);

		// if ($attachments) {
		// 	foreach ($attachments as $attachment) {
		// 		$parent_id = (!$last_comment) ? '' : $last_comment;
		// 		$attachment_type = ($subject == '' && $attachment['attachment_type'] !== '') ? $attachment['attachment_type'] : $subject;
		// 		$subject_id = ($subject_id == '' && $attachment['subject_id'] !== '') ? $attachment['subject_id'] : $subject;
		// 		$settings['user_id'] = $attachment_type;
		// 		$settings['subject'] = $attachment_type;
		// 		$settings['subject_id'] = $subject_id;
		// 		$settings['parent_id'] = $parent_id;
		// 		$settings['type'] = serialize('attachment');
		// 		$settings['message'] = serialize($attachment['attachment_id']);
		// 		$wpdb->insert($table_name, $settings);
		// 	}
		// }
		return $wpdb->insert_id;
	}
}