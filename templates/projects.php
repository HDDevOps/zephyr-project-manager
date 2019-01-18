<?php
	/**
	* Project Page
	* Users can create, view, edit and manage projects from this page
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Categories;
	use Inc\Base\BaseController;

	$Tasks = new Tasks();
	$Projects = new Projects();
	$projects = Projects::get_projects();
	$project_count = Projects::project_count();
	$base_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects'));
?>

<div class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container" class="zpm_add_project">
		<div class="zpm_body" style="display: none;">
			<ul class="nav nav-tabs" >
				<li class="active"><a href="#tab-1">New Project</a></li>
			</ul>
		</div>

		<?php if (isset($_GET['action']) && $_GET['action'] == 'edit_project') : ?>
			<?php include( ZPM_PLUGIN_PATH . '/templates/parts/project-single.php' ); ?>
		<?php else: ?>
			<div id="zpm_projects_holder" class="zpm_body">
				<div id="zpm_project_manager_display" class="<?php echo ($project_count == '0') ? 'zpm_hide' : ''; ?>">
					<div id="zpm_project_page_options">
						<div id="zpm_filter_projects" class="zpm_custom_dropdown zpm_button" data-dropdown-id="zpm_filter_options">
							<span class="zpm_selected_option">Filter Projects</span>
							<ul id="zpm_filter_options" class="zpm_custom_dropdown_options">
								<li class="zpm_selection_option" data-zpm-filter="-1">All Projects</li>
								<li class="zpm_selection_option" data-zpm-filter="1">Incomplete Projects</li>
								<li class="zpm_selection_option" data-zpm-filter="2">Complete Projects</li>
							</ul>
						</div>
						<button id="zpm_create_new_project" class="zpm_button">New Project</button>
					</div>
				</div>

				<!-- No projects yet -->
				<?php if ($project_count == '0') : ?>
					<div class="zpm_no_results_message">No projects created yet. To create a project, click on the 'Add' button at the top right of the screen or click <a id="zpm_first_project" class="zpm_button_link">here</a></div>
				<?php endif; ?>

				<!-- Project list/grid -->
				<div id="zpm_project_list">
					<?php include( ZPM_PLUGIN_PATH . '/templates/parts/project_grid.php' ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php $this->get_footer(); ?>