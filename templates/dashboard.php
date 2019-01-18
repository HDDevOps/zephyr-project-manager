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
	$notice_version = '1.3';
	$user_id = get_current_user_id();
	Utillities::save_user_settings( $user_id, array(
		'hide_dashboard_widgets' => true
	) );
	$settings = Utillities::get_user_settings( $user_id );
?>

<div class="zpm_settings_wrap">
	<?php if (!get_option('zpm_first_time')) : ?>
		<?php include('welcome.php'); ?>
	<?php elseif ($this->is_pro() && !get_option('zpm_welcome_pro')) : ?>
		<?php include( ZEPHYR_PRO_PLUGIN_PATH . 'views/welcome.php'); ?>
	<?php else: ?>
		<?php $this->get_header(); ?>
		<div id="zpm_container">
			<h1 class="zpm_page_title">Dashboard</h1>
			<div class="zpm_panel_container">
				
				<!-- Display Whats New Notice -->
				<?php if ( !Utillities::notice_is_dismissed( $notice_version ) ) : ?>
					<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="<?php echo $notice_version; ?>">
						<h4 class="zpm_panel_title">What's New</h4>

						<div class="zpm-notice-image">
								<img class="zpm-notice-image-left" src="<?php echo ZPM_PLUGIN_URL . "/assets/img/zephyr-tasks-framed.png"; ?>" />
							</div>
						<div class="zpm-notice-content">
							
							<h3 class="zpm-notice-content-title">Zephyr Project Manager Android App has been released!</h3>
							<p>You can now manage all your projects and tasks on the go, from anywhere by linking your Android app to your website.</p>
							<p>* Note, that the app requires the Pro version of Zephyr Project Manager. If you do not already have the Pro version installed, you can purchase it from <a href="https://zephyr-one.com/purchase-pro" class="zpm-link" target="_blank">here.</a></p>
							<p>Get the app now on Google Play Store.</p>
						</div>

						<div class="zpm-notice-buttons">
							<a class="zpm_button" href="https://zephyr-one.com/purchase-pro" target="_blank">Get Android App</a>
							<a class="zpm_button" href="https://zephyr-one.com/purchase-pro" target="_blank">Get Pro Add-On</a>
							<button class="zpm-dismiss-whats-new zpm_button">Dismiss Notice</button>
						</div>
					</div>
				<?php endif; ?>

				<div class="zpm-panel zpm-panel-8">
					<h4 class="zpm_panel_title">Overview</h4>
					<?php
						$project_count = Projects::project_count();
						$completed_projects = Projects::completed_project_count();
						$completed_tasks = Tasks::get_tasks(array('completed' => '1'));
					?>

					<div id="zpm-project-stat-overview">
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value"><?php echo $project_count; ?></span>
							<span class="zpm-project-stat-label">Projects</span>
						</span>
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value good"><?php echo $completed_projects; ?></span>
							<span class="zpm-project-stat-label">Completed Projects</span>
						</span>
						<span class="zpm-project-stat">
							<span class="zpm-project-stat-value good"><?php echo sizeof($completed_tasks); ?></span>
							<span class="zpm-project-stat-label">Completed Tasks</span>
						</span>
					</div>

				</div>
				<div class="zpm-panel zpm-panel-4">
					<h4 class="zpm_panel_title">Tasks due this week</h4>
					<?php $args = array(
						'limit' => 5,
						'assignee' => get_current_user_id()
					);
					$my_tasks = Tasks::get_tasks($args);
					$week_tasks = Tasks::get_week_tasks(get_current_user_id());
					$args = array( 'assignee' => get_current_user_id() );
					$overdue_tasks = Tasks::get_overdue_tasks($args); ?>

					<ul class="zpm_admin_list">
						<?php foreach($week_tasks as $task) : ?>
							<?php $due_date = date('D', strtotime($task->date_due)); ?>
							<li><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class="zpm_link"><?php echo stripslashes($task->name); ?></a><span class="zpm_widget_date zpm_date_pending"><?php echo $due_date; ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php if (empty($week_tasks)) : ?>
						<p>You have no tasks due this week.</p>
					<?php endif; ?>

					<h4 class="zpm_panel_title">Overdue tasks</h4>
					<ul class="zpm_admin_list">
						<?php foreach($overdue_tasks as $task) : ?>
							<?php $due_date = date('d M', strtotime($task->date_due)); ?>
							<li><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class="zpm_link"><?php echo stripslashes($task->name); ?></a><span class="zpm_widget_date zpm_date_overdue"><?php echo $due_date; ?></span></li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')); ?>" class="zpm_button">See All Tasks</a>
					<?php if (empty($overdue_tasks)) : ?>
						<p>You have no overdue tasks.</p>
					<?php endif; ?>
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

					if (empty($dashboard_projects) || $i == 0) {
						?>
							<div class="zpm_no_results_message">Welcome to the Dashboard. To add projects to the dashboard and keep track of important projects, navigate to the <a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); ?>" class="zpm_link">Projects</a> page and click on the options button for the project, then select the option <i>Add to Dashboard</i>.</div>
						<?php
					}
				?>

				<!-- Display Whats New Notice -->
				<?php if ( !Utillities::notice_is_dismissed( 'zpm-patreon-notice' ) ) : ?>
					<div id="zpm-whats-new" class="zpm-panel zpm-panel-12" data-notice="'zpm-patreon-notice'">
						<h4 class="zpm_panel_title">Support me on Patreon</h4>
						<p>If you like the plugin and what I do and would like to help make the plugin better, please consider supporting me on Patreon. This would help a lot in being able to work on the plugin full-time and focus more on it to make it better and add new features. Thank you so much.</p>
						<div class="zpm-notice-buttons">
							
							<button class="zpm-dismiss-notice-button zpm_button" data-notice-version="zpm-patreon-notice">Dismiss Notice</button>
							<a href="https://www.patreon.com/dylanjkotze" target="_blank" class="zpm-patreon-button zpm_button">Support me on Patreon</a>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</div>
	<?php endif; ?>
</div>
<?php $this->get_footer(); ?>