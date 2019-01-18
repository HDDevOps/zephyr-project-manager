<?php
	/**
	* Template for displaying the Projects Edit/View page
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;
	use Inc\Core\Categories;
	use Inc\Base\BaseController;

	$project = Projects::get_project( $_GET['project'] );
	$Task = new Tasks();
	$base_url =  esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); 
	$BaseController = new BaseController;
	$user = $BaseController->get_user_info( $project->user_id);

	$current_user = wp_get_current_user();
	$liked_projects = get_option( 'zpm_liked_projects_' . $current_user->data->ID, false );
	$liked_projects = unserialize($liked_projects);
	$date_due = new DateTime($project->date_due);
	$date_start = new DateTime($project->date_start);
	$project->date_due = ($date_due->format('Y-m-d') !== '-0001-11-30') ? $date_due->format('Y-m-d') : '';
	$project->date_start = ($date_start->format('Y-m-d') !== '-0001-11-30') ? $date_start->format('Y-m-d') : '';
	$project_status = maybe_unserialize( $project->status );
	$project_members = maybe_unserialize( $project->team ) ? maybe_unserialize( $project->team ) : array();
	$members = Utillities::get_users();

	$general_settings = get_option('zpm_general_settings');

	if ( isset($general_settings['project_access'])) {
		switch ($general_settings['project_access']) {
			case '0':
				break;
			case '1':
				if ( empty($project_members) || !in_array( get_current_user_id(), (array) $project_members )) {
					if ((int) get_current_user_id() !== (int) $project->user_id) {
						?>
							<div class="zpm-notice">Sorry, you do not have access to this project.</div>
						<?php
						exit;
					}
				}
				break;
			case '2':
				if ( (int)get_current_user_id() !== (int)$project->user_id ) {
					?>
						<div class="zpm-notice">Sorry, you do not have access to this project.</div>
					<?php
					exit;
				}
				break;
			default:
				break;
		}
	}
?>

<h2 id="zpm_project_name_title" class="zpm_admin_page_title">
	<?php echo $project->name; ?>
</h2>
<small class="zpm_title_information"><?php echo Projects::project_created_by($project->id); ?></small>
<input type="hidden" id="zpm-project-id" value="<?php echo $project->id; ?>">

<div class="zpm_nav_holder zpm_body">
	<nav class="zpm_nav">
		<ul class="zpm_nav_list">
			<li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="1">Overview</li>
			<li class="zpm_nav_item" data-zpm-tab="2">Tasks</li>
			<li class="zpm_nav_item" data-zpm-tab="3">Discussion</li>
			<li class="zpm_nav_item" data-zpm-tab="0">Members</li>
			<li class="zpm_nav_item" id="zpm_update_project_progress" data-zpm-tab="4">Progress</li>
			<?php echo apply_filters( 'zpm-project-tabs', '' ); ?>
		</ul>
	</nav>
</div>

<div id="zpm_project_editor" class="zpm_body <?php echo 'project-type-' . $project->type; ?>" data-project-id="<?php echo $project->id; ?>">
	<!-- Project Overview / Editing -->
	<div class="zpm_tab_pane zpm_tab_active" data-zpm-tab="1">
		<label class="zpm_label" for="zpm_edit_project_name">Name</label>
		<input type="text" id="zpm_edit_project_name" class="zpm_input" name="zpm_edit_project_name" value="<?php echo $project->name; ?>" />
		<label class="zpm_label" for="zpm_edit_project_description">Description</label>
		<textarea id="zpm_edit_project_description" class="zpm_input" name="zpm_edit_project_description"><?php echo $project->description; ?></textarea>

		<label class="zpm_label" for="zpm_edit_project_start_date">Start Date</label>
		<input type="text" id="zpm_edit_project_start_date" placeholder="Start Date"  class="zpm_input" value="<?php echo $project->date_start; ?>"/>
		<label class="zpm_label" for="zpm_edit_project_due_date">Due Date</label>
		<input type="text" id="zpm_edit_project_due_date" placeholder="Due Date" class="zpm_input" value="<?php echo $project->date_due; ?>"/>

		<div class="zpm_project_editor_categories">
			<label class="zpm_label">Categories</label>
			<?php $assigned_categories = unserialize($project->categories); ?>
			<?php $categories = Categories::get_categories(); ?>
			<?php $i = 0 ?>

			<?php if (empty($categories)) : ?>
				<!-- No categories found -->
				<p class="zpm_extra_info zpm_text_italic">There are no categories yet. You can create categories <a href="<?php menu_page_url( 'zephyr_project_manager_categories', true ); ?>" class="zpm_link">here</a>.</p>
			<?php endif; ?>

			<?php foreach ($categories as $category) : ?>
				<?php $checked = (is_array($assigned_categories) && in_array($category->id, $assigned_categories) ? 'checked' : ''); ?>
				<div class="zpm_category_item">
					<label for="category-edit-<?php echo $category->id; ?>" class="zpm_checkbox_label">
						<input type="checkbox" id="category-edit-<?php echo $category->id; ?>" name="zpm_project_edit_category[]" class="zpm_project_edit_categories zpm_toggle" data-category-id="<?php echo $category->id; ?>" value="1" <?php echo $checked; ?>/>
						<div class="zpm_main_checkbox">
							<svg width="20px" height="20px" viewBox="0 0 20 20">
								<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
								<polyline points="4 11 8 15 16 6"></polyline>
							</svg>
						</div>
					</label>
					<div class="zpm_category_name"><?php echo $category->name; ?></div>
				</div>
				<?php $i++; ?>
			<?php endforeach; ?>
		</div>

		<button id="zpm_project_save_settings" class="zpm_button">Save Changes</button>
		<a class="zpm_button" href="<?php echo $base_url; ?>" id="zpm_back_to_projects">Back to Projects</a>

		<div class="zpm_project_options">
			<span id="zpm_like_project_btn" class="zpm_circle_option_btn <?php echo (is_array($liked_projects) && in_array($project->id, $liked_projects)) ? 'zpm_liked' : ''; ?>" data-project-id="<?php echo $project->id; ?>">
				<div class="lnr lnr-thumbs-up"></div>
			</span>
		</div>
	</div>

	<!-- Project Tasks -->
	<?php ob_start(); ?>
	<div id="zpm_project_view_tasks" class="zpm_tab_pane zpm_body" data-zpm-tab="2">
		<button id="zpm_add_new_project_task" class="zpm_button">New Task</button>
		<?php $Task->view_task_list(); ?>
	</div>
	<?php $html = ob_get_clean();
	echo apply_filters('zpm-kanban-tasks', $html); ?>

	<div id="zpm_project_view_discussion" class="zpm_tab_pane" data-zpm-tab="3">
		<h4 class="zpm_panel_heading">Comments</h4>
		<div class="zpm_task_comments">
			<?php $comments = Projects::get_comments( $project->id ); ?>
			<?php foreach($comments as $comment) : ?>
				<?php echo Projects::new_comment($comment); ?>
			<?php endforeach; ?>
		</div>
		<div class="zpm_chat_box_section">
			<div class="zpm_chat_box">
				<div id="zpm_text_editor_wrap">
					<div id="zpm_chat_message" contenteditable="true" placeholder="Write comment..."></div>
					<div class="zpm_editor_toolbar">
						<a href="#" data-command='addCode'><i class='lnr lnr-code'></i></a>
						<a href="#" data-command='createlink'><i class='lnr lnr-link'></i></a>
						<a href="#" data-command='undo'><i class='lnr lnr-undo'></i></a>
					</div>
				</div>
				<div class="zpm_chat_box_footer">
					<button data-project-id="<?php echo $project->id; ?>" id="zpm_project_chat_files" class="zpm_task_chat_files zpm_button">Upload Files</button>
					<button data-project-id="<?php echo $project->id; ?>" id="zpm_project_chat_comment" class="zpm_button">Comment</button>
					<div id="zpm_chat_attachments">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Memebers -->
	<div id="zpm_project_view_progress" class="zpm_tab_pane" data-zpm-tab="0">
		<h4 class="zpm_panel_heading">Members</h4>
		<div>
			<ul class="zpm-member-list">
				<?php foreach ($members as $member) : ?>
					<li><?php echo $member['name']; ?>
						<span class="zpm-memeber-toggle">
							<input type="checkbox" id="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-project-member" data-member-id="<?php echo $member['id']; ?>" <?php echo in_array($member['id'], (array)$project_members) ? 'checked' : ''; ?>>
							<label for="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
							</label>
						</span>
					</li>
				<?php endforeach; ?>
				<button id="zpm-save-project-members" class="zpm_button">Save Members</button>
			</ul>
		</div>
	</div>

	<!-- Progress -->
	<div id="zpm_project_view_progress" class="zpm_tab_pane" data-zpm-tab="4">
		<div id="zpm_project_view_status">
			<h4 class="zpm_panel_heading">Project Status</h4>

			<div id="zpm_project_overview_section">
				<div id="zpm_project_status_colors">
					<span class="zpm_project_status zpm_status_on_track <?php echo isset($project_status['color']) && $project_status['color'] == 'green' ? 'active' : ''; ?>" data-status="green"></span>
					<span class="zpm_project_status zpm_status_pending <?php echo isset($project_status['color']) && $project_status['color'] == 'yellow' ? 'active' : ''; ?>" data-status="yellow"></span>
					<span class="zpm_project_status zpm_status_overdue <?php echo isset($project_status['color']) && $project_status['color'] == 'red' ? 'active' : ''; ?>" data-status="red"></span>
				</div>
				<div id="zpm_project_status" placeholder="Project Status" contentEditable="true">
					<?php echo isset($project_status['status']) ? esc_html($project_status['status']) : ''; ?>
				</div>
				<div id="zpm_project_status_footer">
					<button id="zpm_update_project_status" class="zpm_button" data-project-id="<?php echo $project->id; ?>">Update Status</button>
				</div>
			</div>
		</div>
		<?php
		$project_id = $project->id;
		$report_name = $project->name;

		$project = Projects::get_project($project_id);
		$project_creator = BaseController::get_project_manager_user($project->user_id);
		$description = ($project->description !== '') ? $project->description : '<span class="zpm_subtle_error">No description</span>';
		$date = date('d/m/Y');
		$created_on = new DateTime($project->date_created);
		$start_on = new DateTime($project->date_start);
		$due_on =  new DateTime($project->date_due);
		$start_on = ($start_on->format('Y') !== '-0001') ? $start_on->format('d M Y') : '';
		$due_on = ($due_on->format('Y') !== '-0001') ? $due_on->format('d M Y') : '';
		$task_count = Tasks::get_project_task_count($project_id);
		$completed_tasks = Tasks::get_project_completed_tasks($project_id);
		$args = array( 'project_id' => $project_id );
		$overdue_tasks = sizeof(Tasks::get_overdue_tasks($args));
		$pending_tasks = $task_count - $completed_tasks;
		$percent_complete = ($task_count !== 0) ? floor($completed_tasks / $task_count * 100): '100';
		ob_start();
		?>

		<h4 class="zpm_panel_heading">Progress</h4>
		<canvas class="zpm_report_chart" id="zpm_project_progress_chart" width="400" height="200"></canvas>

		<img id="zpm_project_report_chart_img" style="display: none">
		
		<div class='zpm_report_task_stats'>
			<span class='zpm_report_stat'>
				<label class='zpm_label'>Completed Tasks</label> <?php echo $completed_tasks; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'>Pending Tasks</label> <?php echo $pending_tasks; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'>Overdue Tasks</label> <?php echo $overdue_tasks; ?>
			</span>
			<span class='zpm_report_stat'>
				<label class='zpm_label'>Percent Complete:</label> <?php echo $percent_complete . '%'; ?>
			</span>
		</div>
	</div>

	<?php echo apply_filters( 'zpm-project-tab-pages', '' ); ?>
</div>