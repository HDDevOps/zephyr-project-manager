<?php 
	/**
	* Template for displaying the New Task modal
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;
	use Inc\Core\Members;
	
	$projects = Projects::get_projects();
	$users = Members::get_zephyr_members();
	$date = date('Y-m-d');
?>

<div id="zpm_create_task" class="zpm-modal">
	<h5 class="zpm_modal_header"><?php _e( 'New Task', 'zephyr-project-manager' ); ?></h5>
	<div class="zpm_modal_body">
		<span class="zpm_close_modal">+</span>
		<div class="zpm_modal_content">
			<?php echo apply_filters( 'zpm_new_task_before', '' ); ?>

			<label class="zpm_label" for="zpm_new_task_name"><?php _e( 'Task Name', 'zephyr-project-manager' ); ?></label>
			<input type="text" id="zpm_new_task_name" class="zpm_new_task_name_input zpm_input" placeholder="<?php _e( 'Task Name', 'zephyr-project-manager' ); ?>" />

			<label class="zpm_label" for="zpm_new_task_description"><?php _e( 'Task Description', 'zephyr-project-manager' ); ?></label>
			<textarea id="zpm_new_task_description" class="zpm_input" placeholder="<?php _e( 'Task Description', 'zephyr-project-manager' ); ?>"></textarea>

			<?php if (!isset($_GET['project'])) : ?>
				<label class="zpm_label" for="zpm_new_task_project"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
				<select id="zpm_new_task_project">
					<option value="-1"><?php _e( 'Select Project', 'zephyr-project-manager' ); ?></option>
					<?php foreach ($projects as $project) : ?>
						<option value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
					<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input type="hidden" id="zpm_new_task_project" value="<?php echo $_GET['project'] ?>"/>
			<?php endif; ?>
			
			<label class="zpm_label" for="zpm_new_task_assignee"><?php _e( 'Assignee', 'zephyr-project-manager' ); ?></label>
			<select id="zpm_new_task_assignee">
				<option value="-1"><?php _e( 'Select Assignee', 'zephyr-project-manager' ); ?></option>
				<?php foreach ($users as $user) : ?>
					<option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
				<?php endforeach; ?>
			</select>

			<label class="zpm_label" for="zpm-new-task-team-selection"><?php _e( 'Team', 'zephyr-project-manager' ); ?></label>
			<?php echo Members::team_dropdown_html( 'zpm-new-task-team-selection' ); ?>
		
			<div class="zpm_options_container">
				<span class="zpm_options_col">
					<label class="zpm_label" for="zpm_new_task_start_date"><?php _e( 'Start Date', 'zephyr-project-manager' ); ?></label>
					<input id="zpm_new_task_start_date" placeholder="<?php _e( 'Start Date', 'zephyr-project-manager' ); ?>" value="<?php echo $date; ?>" class="zpm_input" />
				</span>
				<span class="zpm_options_col">
					<label class="zpm_label" for="zpm_new_task_due_date"><?php _e( 'Due Date', 'zephyr-project-manager' ); ?></label>
					<input id="zpm_new_task_due_date" placeholder="<?php _e( 'Due Date', 'zephyr-project-manager' ); ?>" class="zpm_input" />
				</span>
			</div>

			<?php do_action( 'zpm_new_task_settings' ); ?>

			<?php echo apply_filters( 'zpm-task-kanban-id', '' ); ?>
		</div>

		<div class="zpm_modal_buttons">
			<?php if (!BaseController::is_pro()) : ?>
				<button id="zpm_add_custom_field_pro" class="zpm_button" data-zpm-pro-upsell="true">
					<div class="zpm-pro-notice">
						<span class="lnr lnr-cross zpm-close-pro-notice"></span>
					<?php _e( 'This option is only available in the Pro version.', 'zephyr-project-manager' ); ?> <br/><a class="zpm-purchase-link zpm_link" href="<?php echo ZEPHYR_PRO_LINK; ?>" target="_blank"><span class="zpm-purchase-icon lnr lnr-star"></span><?php _e( 'Purchase the Pro Add-On Now', 'zephyr-project-manager' ); ?></a>.
					</div>
					<?php _e( 'Add Custom Field', 'zephyr-project-manager' ); ?>
				</button>
			<?php endif; ?>

			<?php do_action( 'zpm_new_task_buttons' ); ?>

			<button id="zpm_save_task" class="zpm_button"><?php _e( 'Create Task', 'zephyr-project-manager' ); ?></button>
		</div>
	</div>
</div>