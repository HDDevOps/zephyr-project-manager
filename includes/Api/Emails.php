<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Api;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Base\BaseController;

class Emails {

	/**
	* Sends an email to a give email address
	* Used to send updates, reports and notifcations
	* @param string $to The email of the recipient
	* @param string $subject The subject of the email
	* @param string $message_subject
	* @param int $subject_id
	* @param string $message
	*/
	public static function send_email( $to, $subject, $message ) {
		add_filter('wp_mail_content_type', array('Inc\Api\Emails', 'set_html_content_type'));
		add_filter('wp_mail_from', array('Inc\Api\Emails', 'do_email_filter'));
		add_filter('wp_mail_from_name', array('Inc\Api\Emails', 'do_email_name_filter'));
		// switch ($message_subject) {
		// 	case 'task':
		// 		$message_output = Emails::task_email_template( $subject_id );
		// 		break;
		// 	case 'project':
		// 		$message_output = Emails::project_template( $subject_id );
		// 		break;
		// 	case 'task_notifications':
		// 		$message_output = Emails::task_notifications_template( $subject_id );
		// 		break;
		// 	default:
		// 		$message_output = Emails::email_template( $message, $subject );
		// 		break;
		// }
		
		if (wp_mail( $to, $subject, $message)) {
			
		} else {
			
		}   

		remove_filter( 'wp_mail_content_type', array('Inc\Api\Emails', 'set_html_content_type') );
	}



	// define the wp_mail_failed callback 
	public static function action_mail_failed($wp_error) {
	    return error_log(print_r($wp_error, true));
	}

	public static function set_html_content_type() {
		return 'text/html';
	}

	public static function do_email_filter(){
		return 'no-reply@zephyr-one.com';
	}

	public static function do_email_name_filter(){
		return 'Zephyr Project Manager';
	}

	public static function email_template( $header, $body, $footer ) {
		ob_start();
		include(ZPM_PLUGIN_PATH . '/templates/email_templates/email_template.php');
		$email_content = ob_get_clean();
		return $email_content;
	}

	public static function task_email_template( $subject_id ) {
		ob_start();
		include(ZPM_PLUGIN_PATH . '/templates/email_templates/task_email.php');
		$email_content = ob_get_clean();
		return $email_content;
	}

	public static function project_template( $project_id ) {
		ob_start();
		include(ZPM_PLUGIN_PATH . '/templates/email_templates/project_email.php');
		$email_content = ob_get_clean();
		return $email_content;
	}

	public static function task_notifications_template( $subject_id ) {
		ob_start();
		include(ZPM_PLUGIN_PATH . '/templates/email_templates/task_notifications_email.php');
		$email_content = ob_get_clean();
		return $email_content;
	}

	/**
	* Sends an email update to all users depending on their notification preferences
	*/
	public static function send_updates( $message = null, $subject = null, $subject_id ) {
		$users = get_users();
		$project_managers = [];
		// foreach ($users as $user) {
		// 	$user_id = $user->ID;
		// 	$user = BaseController::get_project_manager_user($user_id);
		// 	$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		// 	$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		// 	$project_managers[] = [
		// 		'email' => $email,
		// 		'name'  => $name,
		// 		'preferences' => $user['preferences']
		// 	];
		// 	if ($user['preferences']['notify_activity']) {
		// 		Emails::task_email_template( $subject_id );
		// 	}
		// }
	}

	/**
	* Sends a weekly email update of projects to all users
	*/
	public static function weekly_updates( $projects ) {
		$users = BaseController::get_users();

		foreach ($users as $user) {
			$user_id = $user->ID;
			$user = BaseController::get_project_manager_user($user_id);
			$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
			$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;

			
			if ($user['preferences']['notify_updates']) {
				foreach ($projects as $project) {
					$header = __( 'Weekly Updates', 'zephyr-project-manager' ) . ' - ' . stripslashes($project->name);

					$task_count = Tasks::get_project_task_count($project->id);
					$completed_tasks = Tasks::get_project_completed_tasks($project->id);
					$args = array( 'project_id' => $project->id );
					$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
					$pending_tasks = $task_count - $completed_tasks;
					$percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100): '100';
					if ($task_count <= 0) {
						continue;
					}
					ob_start();
					?>

					<div class="tasks_section">
						<span class="task_item">
							<div class="task_count"><?php echo $task_count; ?></div>
							<div class="task_subject"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></div>
						</span>
						<span class="task_item">
							<div class="task_count"><?php echo $completed_tasks; ?></div>
							<div class="task_subject"><?php _e( 'Completed', 'zephyr-project-manager' ); ?></div>
						</span>
						<span class="task_item">
							<div class="task_count"><?php echo $pending_tasks; ?></div>
							<div class="task_subject"><?php _e( 'Pending', 'zephyr-project-manager' ); ?></div>
						</span>
						<span class="task_item">
							<div class="task_count"><?php echo $percent_complete; ?>%</div>
							<div class="task_subject"><?php _e( 'Complete', 'zephyr-project-manager' ); ?></div>
						</span>
					</div>
					<?php
					$body = ob_get_clean();

					$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_projects"));
					$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . '" style="color: #fff; text-decoration: none;">' . __( 'View in WordPress', 'zephyr-project-manager' ) . '</a></button>';

					$html = Emails::email_template($header, $body, $footer);
					Emails::send_email($email, 'Zephyr - ' . __( 'Weekly Updates', 'zephyr-project-manager' ), $html);
				}
			}
		}
	}

	/**
	* Sends a weekly email update of projects to all users
	* @param array $tasks Array of overdue tasks
	*/
	public static function task_notifications( $tasks ) {
		$users = BaseController::get_users();

		if (sizeof($tasks) >= 0) {
			foreach ($users as $user) {
				$user_id = $user->ID;
				$user = BaseController::get_project_manager_user($user_id);
				$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
				$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
				
				if ($user['preferences']['notify_tasks']) {
					$subject = __( 'Tasks due this week', 'zephyr-project-manager' );
					$header = __( 'You have the following due tasks this week', 'zephyr-project-manager' );

					ob_start();
					$i = 0;
					foreach ($tasks as $task) : ?>
						<?php
							$date = new DateTime();
							$original = new DateTime($task->date_due);
							$overdue = '';
							$due_date = $original->format('Y') !== '-0001' ? $original->format('d M') : __( 'No date set', 'zephyr-project-manager' );
							$overdue = ($date->format('Y-m-d') > $original->format('Y-m-d')) ? 'overdue' : '';
						?>
						<?php if ($user_id == $task->assignee) : ?>
							<div class="email_task">
								<?php echo $task->name; ?>
								<span class="email_task_date <?php echo $overdue; ?>"><?php echo $due_date; ?></span>
							</div>
						<?php endif; ?>
						<?php $i++; ?>
					<?php endforeach;
					$body = ob_get_clean();

					$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_tasks"));
					$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . '" style="color: #fff; text-decoration: none;">' . __( 'View Tasks in WordPress', 'zephyr-project-manager' ) . '</a></button>';
					if ($i > 0) {
						//$html = Emails::email_template($header, $body, $footer);
						//Emails::send_email($email, $subject, $html);
					}
				}
			}
		}
	}

	/**
	* Sends an email update about a new project to all users depending on their notification preferences
	*/
	public static function new_project_email( $project_id ) {
		// $users = get_users();
		// $creator = BaseController::get_project_manager_user(get_current_user_id());
		// $project_managers = [];

		// foreach ($users as $user) {
		// 	// Do not send email to the person who created the project
		// 	if ($user->ID == get_current_user_id()) {
		// 		continue;
		// 	}
		// 	$user_id = $user->ID;
		// 	$user = BaseController::get_project_manager_user($user_id);
		// 	$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		// 	$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		// 	$project_managers[] = [
		// 		'email' => $email,
		// 		'name'  => $name,
		// 		'preferences' => $user['preferences']
		// 	];
		// 	if ($user['preferences']['notify_activity']) {
		// 		$project = Projects::get_project($project_id);
		// 		$header = 'New Project Created by ' . $creator['name'];
		// 		$body = '<div><span class="zpm_content">' . $creator['name'] . ' has created a new project called <b>' . stripslashes($project->name) . '</b> in Zephyr Project Manager.</span></div>';
		// 		$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_projects&action=edit_project&project="));
		// 		$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . $project->id . '" style="color: #fff; text-decoration: none;">View Project in WordPress</a></button>';
		// 		$html = Emails::email_template($header, $body, $footer);
		// 		Emails::send_email($email, 'New Project: ' . stripslashes($project->name), $html);
		// 	}
		// }
	}

	/**
	* Sends an email update about a new project to all users depending on their notification preferences
	*/
	public static function new_task_email( $task_id, $user_id = null ) {
		$users = get_users();
		$creator = BaseController::get_project_manager_user(get_current_user_id());
		$project_managers = [];

		if ($user_id == get_current_user_id()) {
			return;
		}

		$user = BaseController::get_project_manager_user( $user_id );
		$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		$project_managers[] = [
			'email' => $email,
			'name'  => $name,
			'preferences' => $user['preferences']
		];

		// Only send email to user if they have enabled notifications for new tasks
		if (isset($user['preferences']['notify_task_assigned']) && $user['preferences']['notify_task_assigned']) {
			$task = Tasks::get_task($task_id);

			$header = __( 'New task created by', 'zephyr-project-manager' ) . ' ' . $creator['name'];
			$subject = __( 'New Task', 'zephyr-project-manager' );

			if ($task->assignee && $task->assignee !== '-1') {
				$assignee = BaseController::get_project_manager_user($task->assignee);
				if ($task->assignee == $user_id) {
					$subject = __( 'New Task Assigned to You', 'zephyr-project-manager' );
					$header = __( 'New Task Assigned to You', 'zephyr-project-manager' );
					$body = '<div><span id="zpm_user_image" style="background-image: url(' . $creator['avatar'] . ')"></span><span class="zpm_content">' . $creator['name'] . ' ' . __( 'has assigned a new task to you', 'zephyr-project-manager' ) . '.</span></div>';
				} else {
					$subject = __( 'New Task Assigned to You', 'zephyr-project-manager' );
					$header = __( 'New Task', 'zephyr-project-manager' ) . ': ' . $task->name;
					$body = '<div><span class="zpm_content">' . $creator['name'] . ' ' . __( 'has created and assigned a new task to', 'zephyr-project-manager' ) . ' ' . $assignee['name'] . '.</span></div>';
				}
			}
			

			$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id="));
			$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . $task->id . '" style="color: #fff; text-decoration: none;">' . __( 'View Task in WordPress', 'zephyr-project-manager' ) . '</a></button>';
			$html = Emails::email_template($header, $body, $footer);
			Emails::send_email($email, $subject, $html);
		}

		// if ( !is_null( $user_id ) ) {
		// 	// Send to single user/assignee
			
		// } else {
		// 	foreach ($users as $user) {
		// 		// Send to all users
		// 		// Do not send email to the person who created the project
		// 		if ($user->ID == get_current_user_id()) {
		// 			continue;
		// 		}

		// 		$user_id = $user->ID;
		// 		$user = BaseController::get_project_manager_user($user_id);
		// 		$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		// 		$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		// 		$project_managers[] = [
		// 			'email' => $email,
		// 			'name'  => $name,
		// 			'preferences' => $user['preferences']
		// 		];
		// 		if ($user['preferences']['notify_activity']) {
		// 			$task = Tasks::get_task($task_id);
		// 			$header = 'New task created by ' . $creator['name'];
		// 			$subject = 'New Task';

		// 			if ($task->assignee && $task->assignee !== '-1') {
		// 				$assignee = BaseController::get_project_manager_user($task->assignee);
		// 				if ($task->assignee == $user_id) {
		// 					$subject = 'New task assigned to you';
		// 					$header = 'New task assigned to you';
		// 					$body = '<div><span id="zpm_user_image" style="background-image: url(' . $creator['avatar'] . ')"></span><span class="zpm_content">' . $creator['name'] . ' has assigned a new task to you.</span></div>';
		// 				} else {
		// 					$subject = 'New task assigned to you';
		// 					$header = 'New Task: ' . $task->name;
		// 					$body = '<div><span class="zpm_content">' . $creator['name'] . ' has created and assigned a new task to ' . $assignee['name'] . '.</span></div>';
		// 				}
		// 			}
					

		// 			$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id="));
		// 			$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . $task->id . '" style="color: #fff; text-decoration: none;">View Task in WordPress</a></button>';
		// 			$html = Emails::email_template($header, $body, $footer);
		// 			Emails::send_email($email, $subject, $html);
		// 		}
		// 	}
		// }
	}

	/**
	* Sends an email update about a deleted task
	*/
	public static function delete_task_email( $task_id ) {
		$users = get_users();
		$creator = BaseController::get_project_manager_user(get_current_user_id());
		$project_managers = [];
		// foreach ($users as $user) {
		// 	// Do not send email to the person who created the project
		// 	if ($user->ID == get_current_user_id()) {
		// 		continue;
		// 	}
			
		// 	$user_id = $user->ID;
		// 	$user = BaseController::get_project_manager_user($user_id);
		// 	$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		// 	$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		// 	$project_managers[] = [
		// 		'email' => $email,
		// 		'name'  => $name,
		// 		'preferences' => $user['preferences']
		// 	];
		// 	if ($user['preferences']['notify_activity']) {
		// 		$task = Tasks::get_task($task_id);
		// 		$subject = 'Task Deleted: ' . $task->name;
		// 		$header = 'Task has been deleted';
		// 		$body = '<div><span class="zpm_content">' . $creator['name'] . ' has deleted the task <b>' . $task->name . '</b>.</span></div>';
				
		// 		$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_tasks"));
		// 		$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . '" style="color: #fff; text-decoration: none;">View other tasks</a></button>';
		// 		$html = Emails::email_template($header, $body, $footer);
		// 		Emails::send_email($email, $subject, $html);
		// 	}
		// }
	}

	/**
	* Sends an email update about a deleted project
	*/
	public static function deleted_project_email( $project_id ) {
		$users = get_users();
		$creator = BaseController::get_project_manager_user(get_current_user_id());
		$project_managers = [];

		// foreach ($users as $user) {
		// 	// Do not send email to the person who created the project
		// 	if ($user->ID == get_current_user_id()) {
		// 		continue;
		// 	}
			
		// 	$user_id = $user->ID;
		// 	$user = BaseController::get_project_manager_user($user_id);
		// 	$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		// 	$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		// 	$project_managers[] = [
		// 		'email' => $email,
		// 		'name'  => $name,
		// 		'preferences' => $user['preferences']
		// 	];
		// 	if ($user['preferences']['notify_activity']) {
		// 		$project = Projects::get_project($project_id);
		// 		$subject = 'Project Deleted: ' . $project->name;
		// 		$header = 'Project has been deleted';
		// 		$body = '<div><span class="zpm_content">' . $creator['name'] . ' has deleted the project <b>' . stripslashes($project->name) . '</b>.</span></div>';
				
		// 		$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_projects"));
		// 		$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . '" style="color: #fff; text-decoration: none;">View other projects</a></button>';
		// 		$html = Emails::email_template($header, $body, $footer);
		// 		Emails::send_email($email, $subject, $html);
		// 	}
		// }

	}

	public static function task_date_change_email( $id, $task_name, $date_due ) {
		$creator = BaseController::get_project_manager_user(get_current_user_id());
		$project_managers = [];
		$task = Tasks::get_task( $id );

		// Do not send email to the person who created the project
		if ($user->ID == get_current_user_id()) {
			return;
		}

		if ($task->assignee == "" || $task->assignee == "-1") {
			return;
		}

		$user = get_user_by( 'ID', $task->assignee );
		
		$user_id = $user->ID;
		$user = BaseController::get_project_manager_user($user_id);
		$email = $user['email'] !== '' ? $user['email'] : wp_get_current_user()->user_email;
		$name = $user['name'] !== '' ? $user['name'] : wp_get_current_user()->display_name;
		$project_managers[] = [
			'email' => $email,
			'name'  => $name,
			'preferences' => $user['preferences']
		];
		if ($user['preferences']['notify_activity']) {
			$date = $date_due->format('d M');
			$subject = __( 'Task Date Changed', 'zephyr-project-manager' );
			$header = __( 'Task Date Changed', 'zephyr-project-manager' );
			;
			$body = '<div><span class="zpm_content">' . sprintf( __( '%s has changed the date of the task %s to %s.', 'zephyr-project-manager' ), $creator['name'], $task_name, $date ) . '</span></div>';
			
			$link = esc_url(admin_url("/admin.php?page=zephyr_project_manager_tasks&action=view_task&task_id="));
			$footer = '<button id="zpm_action_button" style="padding: 10px;"><a href="' . $link . $id . '" style="color: #fff; text-decoration: none;">' . __( 'View Task in WordPress.', 'zephyr-project-manager' ) . '</a></button>';
			$html = Emails::email_template($header, $body, $footer);
			Emails::send_email($email, $subject, $html);
		}
	}
}