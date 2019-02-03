<?php 
	/**
	* Template for displaying the 'task view/task editor' page
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Members;
	use Inc\Base\BaseController;

	$Tasks = new Tasks();
	$BaseController = new BaseController();
	$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : '-1';
	$this_task = ($Tasks->get_task($task_id) !== null) ? $Tasks->get_task($task_id) : '';
	$projects = Projects::get_projects();
	if (!is_object($this_task)) {
		?>
			<p>This task does not exist or has been deleted.</p>
		<?php
		exit();
	}
	$subtasks = $Tasks->get_subtasks($task_id);
	$user = $BaseController->get_user_info($this_task->user_id); 
	$due_datetime = new DateTime($this_task->date_due);
	$start_datetime = new DateTime($this_task->date_start);
	$start_date = ($start_datetime->format('Y-m-d') !== '-0001-11-30') ? $start_datetime->format('Y-m-d') : '';
	$due_date = ($due_datetime->format('Y-m-d') !== '-0001-11-30') ? $due_datetime->format('Y-m-d') : '';
?>

<!-- Task Editor -->
<input type="hidden" id="zpm_js_task_id" value="<?php echo $this_task->id; ?>"/>

<label for="zpm_task_id_<?php echo $this_task->id; ?>" class="zpm-material-checkbox">
	<input type="checkbox" id="zpm_task_id_<?php echo $this_task->id; ?>" name="zpm_task_id_<?php echo $this_task->id; ?>" class="zpm_task_mark_complete zpm_toggle invisible" value="1" <?php echo $this_task->completed == '1' ? 'checked' : ''; ?> data-task-id="<?php echo $this_task->id; ?>">
	<span class="zpm-material-checkbox-label"></span>
</label>

<h2 id="zpm_task_name_title" class="zpm_admin_page_title"><?php echo $this_task->name; ?></h2>
<small class="zpm_title_information"><?php echo $Tasks->task_created_by($this_task->id); ?></small>
<div id="zpm_task_editor" class="zpm_body" data-task_id="<?php $this_task->id; ?>">
	<div class="container">
		<div id="zpm_task_editor_settings" class="col-md-6">
	
	 		<!-- Task Name -->
			<div class="zpm_options_row">

				<div class="zpm-form__group">
				  <input type="text" name="zpm_edit_task_name" id="zpm_edit_task_name" class="zpm-form__field" placeholder="<?php _e( 'Task Name', 'zephyr-project-manager' ); ?>" value="<?php echo $this_task->name; ?>">
				  <label for="zpm_edit_task_name" class="zpm-form__label"><?php _e( 'Task Name', 'zephyr-project-manager' ); ?></label>
				</div>

			</div>

				<!-- Task Description -->
			<div class="zpm_options_row">
		<!-- 		<label class="zpm_label" for="zpm_edit_task_description"><?php _e( 'Task Description', 'zephyr-project-manager' ); ?></label>
				<textarea id="zpm_edit_task_description" class="zpm_input" name="zpm_edit_project_description" placeholder="<?php _e( 'Task Description', 'zephyr-project-manager' ); ?>"><?php echo stripslashes($this_task->description); ?></textarea>
 -->
				<div class="zpm-form__group">
				  <textarea name="zpm_edit_project_description" id="zpm_edit_task_description" class="zpm-form__field" placeholder="<?php _e( 'Task Description', 'zephyr-project-manager' ); ?>" value="<?php echo $this_task->name; ?>"><?php echo stripslashes($this_task->description); ?></textarea>
				  <label for="zpm_edit_task_description" class="zpm-form__label"><?php _e( 'Task Description', 'zephyr-project-manager' ); ?></label>
				</div>
			</div>

			<!-- Start Date -->
			<div class="zpm-form__group">
			  <input type="text" name="zpm_edit_task_start_date" id="zpm_edit_task_start_date" class="zpm-form__field" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $start_date; ?>">
			  <label for="zpm_edit_task_start_date" class="zpm-form__label"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
			</div>

<!-- 
			<label class="zpm_label" for="zpm_edit_task_start_date"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
			<input id="zpm_edit_task_start_date" class="zpm_input" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $start_date; ?>" /> -->

			<!-- Due Date -->
			<div class="zpm-form__group">
			  <input type="text" name="zpm_edit_task_due_date" id="zpm_edit_task_due_date" class="zpm-form__field" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $due_date; ?>">
			  <label for="zpm_edit_task_due_date" class="zpm-form__label"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
			</div>

			<!-- <label class="zpm_label" for="zpm_edit_task_due_date"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
			<input id="zpm_edit_task_due_date" class="zpm_input" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" value="<?php echo $due_date; ?>" />
			 -->
			<!-- Select Assignee -->
			<label class="zpm_label" for="zpm_edit_task_assignee"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?></label>
			<select id="zpm_edit_task_assignee" class="zpm_input zpm-input-chosen">
				<option value="-1"><?php _e( 'Select Assignee', 'zephyr-project-manager' ); ?></option>
				<?php $assigned_user = $this_task->assignee; ?>
				<?php foreach ($users as $user) : ?>
					<option <?php echo ($user['id'] == $assigned_user) ? 'selected' : ''; ?> value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
				<?php endforeach; ?>
			</select>

			<!-- Select Team -->
			<label class="zpm_label" for="zpm-edit-task-team-selection"><?php _e( 'Team', 'zephyr-project-manager' ); ?></label>
			<?php echo Members::team_dropdown_html( 'zpm-edit-task-team-selection', $this_task->team ); ?>

			<!-- Select Project -->
			<label class="zpm_label" for="zpm_edit_task_project"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
			<select id="zpm_edit_task_project" class="zpm_input zpm-input-chosen">
				<option value="-1"><?php _e( 'Select Project', 'zephyr-project-manager' ); ?></option>
				<?php foreach ($projects as $single_project) : ?>
					<option value="<?php echo $single_project->id; ?>" <?php echo $this_task->project == $single_project->id ? 'selected' : ''; ?>><?php echo $single_project->name; ?></option>
				<?php endforeach; ?>
			</select>

			<?php do_action('zpm_after_task_settings', $task_id); ?>
					
			<?php if (!BaseController::is_pro()) : ?>
				<a id="zpm_add_custom_field_pro" class="zpm_button" data-zpm-pro-upsell="true">
					<?php _e( 'Add Custom Field', 'zephyr-project-manager' ); ?>
					<div class="zpm-pro-notice">
						<span class="lnr lnr-cross zpm-close-pro-notice"></span>
						<?php _e( 'This option is only available in the Pro version.', 'zephyr-project-manager' ); ?> <br/><a class="zpm-purchase-link zpm_link" href="<?php echo ZEPHYR_PRO_LINK; ?>" target="_blank"><span class="zpm-purchase-icon lnr lnr-star"></span><?php _e( 'Purchase the Pro Add-On Now', 'zephyr-project-manager' ); ?></a>.
					</div>
					
				</a>
			<?php endif; ?>
			<a id="zpm_save_changes_task" name="zpm_save_changes_task" class="zpm_button" data-task-id="<?php echo $this_task->id; ?>"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></a>
			<a class="zpm_button" href="<?php echo $base_url; ?>" id="zpm_back_to_projects"><?php _e( 'Back to Tasks', 'zephyr-project-manager' ); ?></a>
			<?php do_action( 'zpm_edit_task_buttons' ); ?>

			<div class="zpm_project_options">
				<span id="zpm_like_task_btn" class="zpm_circle_option_btn <?php echo (in_array($this_task->id, (array) $liked_tasks)) ? 'zpm_liked' : ''; ?>" data-task-id="<?php echo $this_task->id; ?>">
					<div class="lnr lnr-thumbs-up"></div>
				</span>
			</div>
		</div>
	</div>
</div>

<!-- Task Subtasks -->
<div id="zpm_edit_task_comments" class="zpm_body">
	<h3><?php _e( 'Subtasks', 'zephyr-project-manager' ); ?></h3>
	<div id="zpm_task_editor_subtasks" class="col-md-6">
		<ul id="zpm_subtask_list">
			<?php foreach( (array) $subtasks as $subtask) : ?>
				<li class="zpm_subtask_item <?php echo $subtask->completed !== '0' ? 'zpm_task_complete' : ''; ?>" data-zpm-subtask="<?php echo $subtask->id ?>">


					<label for="zpm_subtask_<?php echo $subtask->id; ?>" class="zpm-material-checkbox">
						<input type="checkbox" id="zpm_subtask_<?php echo $subtask->id; ?>" class="zpm_subtask_is_done" data-task-id="<?php echo $subtask->id ?>" <?php echo $subtask->completed !== '0' ? 'checked' : ''; ?>>
						<span class="zpm-material-checkbox-label"></span>
					</label>

					<span class="zpm_subtask_name"><?php echo $subtask->name ?></span>
					<span data-zpm-subtask-id="<?php echo $subtask->id ?>" class="zpm_update_subtask"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></span>
					<span data-zpm-subtask-id="<?php echo $subtask->id ?>" class="zpm_delete_subtask"><?php _e( 'Delete', 'zephyr-project-manager' ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<button id="zpm_add_new_subtask" class="zpm_button"><?php _e( 'New Subtask', 'zephyr-project-manager' ); ?></button>
	</div>
</div>

<!-- Task Comments -->
<div id="zpm_edit_task_comments" class="zpm_body">
	<h3><?php _e( 'Discussion', 'zephyr-project-manager' ); ?></h3>
	<div class="zpm_task_comments" data-task-id="<?php echo $this_task->id; ?>">
		<?php $comments = Tasks::get_comments( $this_task->id ); ?>
		<?php foreach($comments as $comment) : ?>
			<?php echo Tasks::new_comment($comment); ?>
		<?php endforeach; ?>
	</div>

	<!-- Task Chat Box -->
	<div class="zpm_chat_box_section">
		<div class="zpm_chat_box">
			<div id="zpm_text_editor_wrap">
				<div id="zpm_chat_message" contenteditable="true" placeholder="<?php _e( 'Write comment...', 'zephyr-project-manager' ); ?>"></div>
				<div class="zpm_editor_toolbar">
					<a href="#" data-command='addCode'><i class='lnr lnr-code'></i></a>
					<a href="#" data-command='createlink'><i class='lnr lnr-link'></i></a>
					<a href="#" data-command='undo'><i class='lnr lnr-undo'></i></a>
				</div>
			</div>
			<div class="zpm_chat_box_footer">
				<button data-task-id="<?php echo $this_task->id; ?>" id="zpm_task_chat_files" class="zpm_task_chat_files zpm_button"><?php _e( 'Upload Files', 'zephyr-project-manager' ); ?></button>
				<button data-task-id="<?php echo $this_task->id; ?>" id="zpm_task_chat_comment" class="zpm_button"><?php _e( 'Comment', 'zephyr-project-manager' ); ?></button>
				<div id="zpm_chat_attachments">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Task Comments -->

<?php
	do_action( 'zpm_after_task_page');
?>