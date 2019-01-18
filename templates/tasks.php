<?php 
	/**
	* Tasks Page
	* Page where all tasks are listed and users can create, view, edit and manage them
	*/
	
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Members;

	$users = Members::get_members();
	$current_user = wp_get_current_user();
	$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_tasks') );
	$liked_tasks = unserialize(get_option( 'zpm_liked_tasks_' . $current_user->data->ID, false ));
	$followed_tasks = unserialize(get_option( 'zpm_followed_tasks_' . $current_user->data->ID, false ));
	$task_count = Tasks::get_task_count();
?>

<div class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<?php if (isset($_GET['action']) && $_GET['action'] == 'view_task') : ?>
			<div id="zpm_task_view">
				<?php include( ZPM_PLUGIN_PATH . '/templates/parts/task-single.php' ); ?>
			</div>
		<?php else: ?>
			
			<!-- There are no tasks yet -->
			<div class="zpm_no_results_message" style="<?php echo ($task_count > 0) ? 'display: none;' : ''; ?>">No tasks created yet. To create a task, click on the 'Add' button at the top right of the screen or click <a id="zpm_first_task" class="zpm_button_link">here</a></div>
			
			<div id="zpm_task_option_container" class="<?php echo $task_count <= 0 ? 'zpm_hidden' : ''; ?>">
				<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown">
					<span class="lnr lnr-menu"></span>
					<div class="zpm_modal_dropdown" id="zpm_view_task_dropdown">
						<ul class="zpm_modal_list">
							<li id="zpm_export_task">
								Export Tasks
								<div class="zpm_export_dropdown">
									<ul>
										<li id="zpm_export_all_tasks_to_csv">Export to CSV</li>
										<li id="zpm_export_all_tasks_to_json">Export to JSON</li>
									</ul>
								</div>
							</li>
							<li id="zpm_import_task">
								Import Tasks 
								<div class="zpm_export_dropdown">
									<ul>
										<li id="zpm_import_tasks_from_csv">Import from CSV</li>
										<li id="zpm_import_tasks_from_json">Import from JSON</li>
									</ul>
								</div>
							</li>
						</ul>
					</div>
				</span>

				<!-- Task filter options -->
				<div id="zpm-tasks-filter-nav" class="zpm_nav_holder zpm_body">
					<nav class="zpm_nav">
						<ul class="zpm_nav_list">
							<li class="zpm_nav_item zpm_selection_option zpm_nav_item_selected" data-zpm-filter="0" data-user-id="<?php echo $current_user->data->ID; ?>">My Tasks</li>
							<li class="zpm_nav_item zpm_selection_option" data-zpm-filter="-1">All Tasks</li>
							<li class="zpm_nav_item zpm_selection_option" data-zpm-filter="1">Incomplete Tasks</li>
							<li class="zpm_nav_item zpm_selection_option" id="zpm_update_project_progress" data-zpm-filter="2">Complete Tasks</li>
									</ul>
					</nav>
					<button class="zpm_button" name="zpm_task_add_new" id="zpm_task_add_new">Add New</button>
				</div>

				
			</div>

			<div id="zpm_task_list_container" class="zpm_body <?php echo $task_count <= 0 ? 'zpm_hidden' : ''; ?>">
				<!-- Task List -->
				<div class="zpm_task_container">
					<div id="zpm_task_list" class="zpm_settings_form">
						<?php Tasks::view_task_list( array( 'user_tasks' => get_current_user_id() ) ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- New Subtask Modal -->
<div id="zpm_new_subtask_modal" class="zpm-modal zpm_compact_modal">
	<label class="zpm_label" for="zpm_new_subtask_name">Name</label>
	<input class="zpm_input" id="zpm_new_subtask_name" placeholder="Subtask name"/>
	<button id="zpm_save_new_subtask" class="zpm_button">Create Subtask</button>
</div>

<!-- Edit Subtask Modal -->
<div id="zpm_edit_subtask_modal" class="zpm-modal zpm_compact_modal">
	<label class="zpm_label" for="zpm_edit_subtask_name">Name</label>
	<input class="zpm_input" id="zpm_edit_subtask_name" placeholder="New Subtask Name" value=/>
	<button id="zpm_update_subtask" class="zpm_button">Save Changes</button>
</div>

<?php $this->get_footer(); ?>

<?php do_action( 'zpm_after_task_page' ); ?>