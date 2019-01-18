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
	<h5 class="zpm_modal_header">New Task</h5>
	<div class="zpm_modal_body">
		<span class="zpm_close_modal">+</span>
		<div class="zpm_modal_content">
			<?php echo apply_filters( 'zpm_new_task_before', '' ); ?>

			<label class="zpm_label" for="zpm_new_task_name">Task Name</label>
			<input type="text" id="zpm_new_task_name" class="zpm_new_task_name_input zpm_input" placeholder="Name" />

			<label class="zpm_label" for="zpm_new_task_description">Task Description</label>
			<textarea id="zpm_new_task_description" class="zpm_input" placeholder="Description"></textarea>

			<?php if (!isset($_GET['project'])) : ?>
				<label class="zpm_label" for="zpm_new_task_project">Project</label>
				<select id="zpm_new_task_project">
					<option value="-1">Select Project</option>
					<?php foreach ($projects as $project) : ?>
						<option value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
					<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input type="hidden" id="zpm_new_task_project" value="<?php echo $_GET['project'] ?>"/>
			<?php endif; ?>
			
			<label class="zpm_label" for="zpm_new_task_assignee">Assignee</label>
			<select id="zpm_new_task_assignee">
				<option value="-1">Select Assignee</option>
				<?php foreach ($users as $user) : ?>
					<option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>;
				<?php endforeach; ?>
			</select>

			<label class="zpm_label" for="zpm-new-task-team-selection">Team</label>
			<?php echo Members::team_dropdown_html( 'zpm-new-task-team-selection' ); ?>
		
			<div class="zpm_options_container">
				<span class="zpm_options_col">
					<label class="zpm_label" for="zpm_new_task_start_date">Start Date</label>
					<input id="zpm_new_task_start_date" placeholder="Start Date" value="<?php echo $date; ?>" class="zpm_input" />
				</span>
				<span class="zpm_options_col">
					<label class="zpm_label" for="zpm_new_task_due_date">Due Date</label>
					<input id="zpm_new_task_due_date" placeholder="Due Date" class="zpm_input" />
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
					This option is only available in the Pro version. <br/><a class="zpm-purchase-link zpm_link" href="<?php echo ZEPHYR_PRO_LINK; ?>" target="_blank"><span class="zpm-purchase-icon lnr lnr-star"></span>Purchase the Pro version now</a>.
					</div>
					Add Custom Fields
				</button>
			<?php endif; ?>

			<?php do_action( 'zpm_new_task_buttons' ); ?>

			<button id="zpm_save_task" class="zpm_button">Create Task</button>
		</div>
	</div>
</div>