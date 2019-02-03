<?php
	/**
	* Dashboard Page
	* Allows users to view project information and upcoming tasks at a glance
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;

	$dashboard_projects = Projects::get_dashboard_projects();
	$notice_version = '1.4';
	$user_id = get_current_user_id();
	Utillities::save_user_settings( $user_id, array(
		'hide_dashboard_widgets' => true
	) );
	$settings = Utillities::get_user_settings( $user_id );
	$project_count = Projects::project_count();
	$completed_projects = Projects::completed_project_count();
	$active_projects = $project_count - $completed_projects;
	$task_count = Tasks::get_task_count();
	$completed_tasks = sizeof( Tasks::get_tasks( array( 'completed' => '1' ) ) );
	$active_tasks = $task_count - $completed_tasks;
	$args = array(
		'limit' => 5,
		'assignee' => get_current_user_id()
	);
	$my_tasks = Tasks::get_tasks($args);
	$week_tasks = Tasks::get_week_tasks(get_current_user_id());
	$args = array(
		'assignee' => get_current_user_id()
	);
	$overdue_tasks = Tasks::get_overdue_tasks($args);
?>

<div class="zpm_settings_wrap">
	<?php if (!get_option('zpm_first_time')) : ?>
		<?php include('welcome.php'); ?>
	<?php elseif ($this->is_pro() && !get_option('zpm_welcome_pro')) : ?>
		<?php include( ZEPHYR_PRO_PLUGIN_PATH . 'views/welcome.php'); ?>
	<?php else: ?>
		<?php $this->get_header(); ?>
		<div id="zpm_container">
			<h1 class="zpm_page_title"><?php _e( 'Dashboard', 'zephyr-project-manager' ); ?></h1>
			<div class="zpm_panel_container">
				
				<!-- Display Whats New Notice -->
				<!-- <?php if ( !Utillities::notice_is_dismissed( $notice_version ) ) : ?>
					<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="<?php echo $notice_version; ?>">
						<h4 class="zpm_panel_title"><?php _e( 'What\'s new?', 'zephyr-project-manager' ); ?></h4>

						<div class="zpm-notice-image">
								<img class="zpm-notice-image-left" src="<?php echo ZPM_PLUGIN_URL . "/assets/img/zephyr-tasks-framed.png"; ?>" />
							</div>
						<div class="zpm-notice-content">
							
							<h3 class="zpm-notice-content-title">Android App is now available to all users - basic and Pro</h3>
							<p>You can now manage all your projects and tasks on the go, from anywhere by linking your Android app to your website.</p>
							<p>You will also get real time notifications of new tasks created and new tasks assigned to you as well as comments and messages on tasks and projects.</p>
							<p><a href="https://play.google.com/store/apps/details?id=com.zephyr.dylank.zephyrprojectmanager">Get the app now on Google Play Store.</a></p>
						</div>

						<div class="zpm-notice-buttons">
							<a class="zpm_button" href="https://play.google.com/store/apps/details?id=com.zephyr.dylank.zephyrprojectmanager" target="_blank"><?php _e( 'Get the Android App', 'zephyr-project-manager' ); ?></a>
							<a class="zpm_button" href="https://zephyr-one.com/purchase-pro" target="_blank"><?php _e( 'Get the Pro Add-On', 'zephyr-project-manager' ); ?></a>
							<button class="zpm-dismiss-whats-new zpm_button"><?php _e( 'Dismiss Notice', 'zephyr-project-manager' ); ?></button>
						</div>
					</div>
				<?php endif; ?> -->

				<div class="zpm-grid-container">
					<div class="zpm-grid-row zpm-grid-row-12">
						<div class="zpm-grid-item zpm-grid-item-3">
							<div class="zpm-material-card zpm-material-card-colored zpm-card-color-blue">
								<h4 class="zpm-card-header"><?php _e( 'Projects Overview', 'zephyr-project-manager' ); ?></h4>
									<div class="zpm-stat-list-item">
										<span class="zpm-stat-value"><?php echo $project_count; ?></span>
										<?php _e( 'Projects', 'zephyr-project-manager' ); ?>
									</div>
									<div class="zpm-stat-list-item">
										<span class="zpm-stat-value"><?php echo $completed_projects; ?></span>
										<?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?>
									</div>
									<div class="zpm-stat-list-item">
										<span class="zpm-stat-value"><?php echo $active_projects; ?></span>
										<?php _e( 'Active Projects', 'zephyr-project-manager' ); ?>
									</div>
							</div>
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<div class="zpm-material-card zpm-material-card-colored zpm-card-color-purple">
								<h4 class="zpm-card-header"><?php _e( 'Tasks Overview', 'zephyr-project-manager' ); ?></h4>
								<div class="zpm-stat-list-item">
									<span class="zpm-stat-value"><?php echo $task_count; ?></span>
									<?php _e( 'Tasks Total', 'zephyr-project-manager' ); ?>
								</div>
								<div class="zpm-stat-list-item">
									<span class="zpm-stat-value"><?php echo $completed_tasks; ?></span>
									<?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?>
								</div>
								<div class="zpm-stat-list-item">
									<span class="zpm-stat-value"><?php echo $active_tasks; ?></span>
									<?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?>
								</div>
							</div>
						</div>
						<div class="zpm-grid-item zpm-grid-item-3">
							<div class="zpm-material-card zpm-material-card-colored zpm-card-color-red">
								<h4 class="zpm-card-header"><?php _e( 'Tasks Due This Week', 'zephyr-project-manager' ); ?></h4>
								<ul class="zpm-tasks-due-list">
								<?php foreach($week_tasks as $task) : ?>
									<?php $due_date = date('D', strtotime($task->date_due)); ?>
									<li class="zpm-tasks-due-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class="zpm_link"><?php echo stripslashes($task->name); ?></a><span class="zpm_widget_date zpm_date_pending"><?php echo $due_date; ?></span></li>
								<?php endforeach; ?>
								</ul>
								
								<?php if (empty($week_tasks)) : ?>
									<p><?php _e( 'You have no tasks due this week', 'zephyr-project-manager' ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="zpm-panel zpm-panel-8">

					<h4 class="zpm_panel_title"><?php _e( 'Overview', 'zephyr-project-manager' ); ?></h4>

					<div id="zpm-project-stat-overview">
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value"><?php echo $project_count; ?></span>
							<span class="zpm-project-stat-label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
						</span>
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value good"><?php echo $completed_projects; ?></span>
							<span class="zpm-project-stat-label"><?php _e( 'Completed Projects', 'zephyr-project-manager' ); ?></span>
						</span>
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value good"><?php echo $completed_tasks; ?></span>
							<span class="zpm-project-stat-label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></span>
						</span>
					</div>

				</div>

				<?php
					$i = 0;
					foreach ($dashboard_projects as $dashboard_project) :
						$project = Projects::get_project($dashboard_project);
						if (!is_object($project)) {
							continue;
						}

						?>
						<div class="<?php echo sizeof($dashboard_projects) > 1 ? 'zpm_panel_6' : 'zpm_panel_12'; ?> zpm_dashboard_project_container">
							<div class="zpm_panel zpm_chart_panel zpm_dashboard_project" data-project-id="<?php echo $project->id; ?>">
								<?php $chart_data = get_option('zpm_chart_data', array()); ?>
								<h4 class="zpm_panel_heading"><?php echo $project->name; ?></h4>
								<span class="zpm_remove_project_from_dashboard lnr lnr-cross-circle"></span>
								<canvas id="zpm_line_chart" class="zpm-dashboard-project-chart" width="600" height="400" data-project-id="<?php echo $project->id; ?>" data-chart-data='<?php echo json_encode($chart_data[$project->id]); ?>'></canvas>

							</div>
						</div>
						<?php
						$i++;
					endforeach;

					if ( empty( $dashboard_projects ) || $i == 0 ) {
						$project_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); ?>
							<div class="zpm_no_results_message">
							<?php printf( __( 'Welcome to the Dashboard. To add projects to the dashboard and keep track of important projects, navigate to the %s Projects %s page and click on the options button for the project, then select the option %s Add to Dashboard %s.', 'zephyr-project-manager' ), '<a href="' . $project_url . '" class="zpm_link">', '</a>', '<i>', '</i>' ); ?>
							</div>
						<?php
					}
				?>

				<!-- Display Patreon Notice -->
				<!-- <?php if ( !Utillities::notice_is_dismissed( 'zpm-patreon-notice' ) ) : ?>
					<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="'zpm-patreon-notice'">
						<h4 class="zpm_panel_title"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></h4>
						<p><?php _e( 'If you like the plugin and what I do and would like to help me improve the plugin more, please consider supporting me on Patreon. This would help a lot in being able to work on the plugin full-time and focus more on it to make it better and add new features. Thank you so much.', 'zephyr-project-manager' ); ?></p>
						<div class="zpm-notice-buttons">
							
							<button class="zpm-dismiss-notice-button zpm_button" data-notice-version="zpm-patreon-notice"><?php _e( 'Dismiss Notice', 'zephyr-project-manager' ); ?></button>
							<a href="https://www.patreon.com/dylanjkotze" target="_blank" class="zpm-patreon-button zpm_button"><?php _e( 'Support me on Patreon', 'zephyr-project-manager' ); ?></a>
						</div>
					</div>
				<?php endif; ?> -->

			</div>
		</div>
	<?php endif; ?>
</div>
<?php $this->get_footer(); ?>