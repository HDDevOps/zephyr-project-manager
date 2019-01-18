<?php
	/**
	* The modal that is used to display a tasks details
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
	
	use Inc\Core\Tasks;
	use Inc\Core\Members;
	use Inc\Base\BaseController;

	$task = Tasks::get_task($task_id);
	$assignee = BaseController::get_project_manager_user($task->assignee);
	$users = get_users();
	$user_id = wp_get_current_user()->ID;
	$tasks_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks'));
	$created_by = Tasks::task_created_by($task_id);
	$subtasks = Tasks::get_subtasks($task_id);
	$my_followed_tasks = unserialize(get_option( 'zpm_followed_tasks_' . $user_id, false ));
	$is_following = in_array($task_id, (array) $my_followed_tasks) ? true : false;
	$followers = array();
	$due_date = new DateTime($task->date_due);
	$start_date = new DateTime($task->start);
	$team = $task->team !== "" ? Members::get_team($task->team) : "";

	foreach ($users as $user) {
		$user_followed_tasks = unserialize(get_option( 'zpm_followed_tasks_' . $user->data->ID, false ));
		if (in_array($task_id, (array) $user_followed_tasks)) {
			$follower = BaseController::get_project_manager_user($user->data->ID);
			$followers[] = $follower;
		}
	}
?>

<input type="hidden" id="zpm_task_view_id" value="<?php echo $task->id; ?>"/>
<div class="zpm_modal_body">
	<div class="zpm_modal_top">
		<label for="zpm_task_id_<?php echo $task->id; ?>" class="zpm_checkbox_label">
			<input type="checkbox" id="zpm_task_id_<?php echo $task->id; ?>" name="zpm_task_id_<?php echo $task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $task->completed == '1' ? 'checked' : ''; ?> data-task-id="<?php echo $task->id; ?>">
			<div class="zpm_main_checkbox">
				<svg width="20px" height="20px" viewBox="0 0 20 20">
					<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
					<polyline points="4 11 8 15 16 6"></polyline>
				</svg>
			</div>
		</label>
		<h3 class="zpm_modal_task_name"><?php echo stripslashes($task->name); ?></h3>
	</div>
	<small class="zpm_title_information"><?php echo $created_by; ?></small>
	<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown">
		<i class="dashicons dashicons-menu"></i>
		<div class="zpm_modal_dropdown" id="zpm_view_task_dropdown">
			<ul class="zpm_modal_list">
				<li id="zpm_delete_task">Delete Task</li>
				<li id="zpm_copy_task">Copy Task</li>
				<li id="zpm_export_task">Export Task 
					<div class="zpm_export_dropdown">
						<ul>
							<li id="zpm_export_task_to_csv">Export to CSV</li>
							<li id="zpm_export_task_to_json">Export to JSON</li>
						</ul>
					</div>
				</li>
				<li id="zpm_convert_task">Convert to Project</li>
				<li id="zpm_print_task">Print</li>
			</ul>
		</div>
	</span>

	<nav class="zpm_nav">
		<ul class="zpm_nav_list">
			<li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="1">Overview</li>
			<li class="zpm_nav_item" data-zpm-tab="2">Discussion</li>
		</ul>
	</nav>

	<div class="zpm_modal_content">
		<div class="zpm_tab_pane zpm_tab_active" data-zpm-tab="1">

			<span id="zpm_task_dates">
				<span id="zpm_task_start_date">
					<label class="zpm_label">Start Date:</label>
					<span class="zpm_task_due_date"><?php echo $start_date->format('d M'); ?></span>
				</span>
				<span id="zpm_task_due_date">
					<label class="zpm_label">Due Date:</label>
					<span class="zpm_task_due_date"><?php echo $due_date->format('d M'); ?></span>
				</span>
			</span>

			<span id="zpm_task_modal_assignee">
				<label class="zpm_label">Assigned to:</label>
				<?php if (!is_null($assignee) && $assignee['id'] !== "") : ?>
					<span class="zpm_task_username"><?php echo $assignee['name']; ?></span>
					<span class="zpm_task_user_avatar" style="background-image: url(<?php echo $assignee['avatar']; ?>);">
					</span>
				<?php else: ?>
					<p class="zpm-no-result-error">Not assigned to member</p>
				<?php endif; ?>
			</span>

			<span id="zpm_task_modal_task_assignee">
				<label class="zpm_label">Assigned Team:</label>

				<?php if ($team !== "") : ?>
					<span class="zpm-task-view-team-name"><?php echo $team['name']; ?></span>
				<?php else: ?>
					<p class="zpm-no-result-error">Not assigned to team</p>
				<?php endif; ?>

			</span>

			<label class="zpm_label">Description:</label>
			<p id="zpm_view_task_description">
				<?php echo $task->description !== "" ? $task->description : "<p class='zpm-no-result-error'>No description added.</p>"; ?>
			</p>
			<div id="zpm_view_task_subtasks">
				<label class="zpm_label">Subtasks</label>
				<ul class="zpm_view_subtasks">
					<?php foreach ($subtasks as $subtask) : ?>
						<li class="zpm_subtask_item <?php echo $subtask->completed == '1' ? 'zpm_task_complete' : ''; ?>" data-zpm-subtask="<?php echo $subtask->id; ?>">
							<input type="checkbox" class="zpm_subtask_is_done" data-task-id="<?php echo $subtask->id; ?>" <?php echo $subtask->completed == '1' ? 'checked' : ''; ?> />
							<span class="zpm_subtask_name"><?php echo $subtask->name; ?></span>
						</li>
					<?php endforeach; ?>

					<?php if (sizeof($subtasks) <= 0) : ?>
						<p class='zpm-no-result-error'>No subtasks created.</p>
					<?php endif; ?>
				</ul>

				<span id="zpm_task_following">
					<span id="zpm_follow_task" class="lnr lnr-plus-circle <?php echo $is_following ? 'zpm_following' : ''; ?>"></span>
					<?php foreach($followers as $follower) : ?>
						<span class="zpm_task_follower" data-user-id="<?php echo $follower['id']; ?>" title="<?php echo $follower['name']; ?>" style="background-image: url('<?php echo $follower['avatar']; ?>');"></span>
					<?php endforeach; ?>
				</span>

			</div>
			<?php do_action('zpm_task_overview', $task_id); ?>
		</div>
		<div class="zpm_tab_pane" data-zpm-tab="2">
			<!-- Discussion -->
			<div class="zpm_task_comments">
				<?php $comments = Tasks::get_comments($task_id); ?>
				<?php foreach ($comments as $comment) : ?>
					<?php echo Tasks::new_comment($comment); ?>
				<?php endforeach; ?>
			</div>

			<!-- Chat box -->
			<div class="zpm_chat_box_section">
				<div class="zpm_chat_box">
					<div id="zpm_text_editor_wrap">
						<div id="zpm_chat_message" contenteditable="true" placeholder="Write comment..."></div>
						<div class="zpm_message_action_buttons">
							<button data-task-id="<?php echo $task_id; ?>" id="zpm_task_chat_files" class="zpm_task_chat_files zpm_button"><span class="zpm_message_action_icon lnr lnr-file-empty"></span></button>
							<button data-task-id="<?php echo $task_id; ?>" id="zpm_task_chat_comment" class="zpm_button"><span class="zpm_message_action_icon lnr lnr-arrow-right"></span></button>
						</div>
					</div>
					<div class="zpm_chat_box_footer">
						<div id="zpm_chat_attachments">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="zpm_modal_buttons">
			<a class="zpm_button" href="<?php echo $tasks_url . '&action=view_task&task_id=' . $task_id; ?>" id="zpm_edit_task_link">Go to Task</a>
	</div> 
</div>