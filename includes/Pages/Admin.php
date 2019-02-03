<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Pages;

use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Utillities;
use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\SanitizationCallbacks;

class Admin extends BaseController {
	public $settings;
	public $callbacks;
	public $callbacks_sanitization;
	public $pages = array();
	public $subpages = array();
	public $access_level;
	
	public function register() {
		
		$access_settings = get_option('zpm_access_settings');

		//$this->access_level = $access_settings ? $access_settings : 'manage_options';
		$this->access_level = 'manage_options';

		if (isset($_POST['zpm_first_time'])) {
			add_option( 'zpm_first_time', true );
		}

		add_action( 'wp_dashboard_setup', array($this, 'setup_dashboard_widget') );

		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks();
		$this->callbacks_sanitization = new SanitizationCallbacks();
		//$this->set_pages();

		$project = new Projects();
		$zpm_used = get_option('zpm_used');

		if (get_option('zpm_first_time')) {
			//$this->set_sub_pages();
		} else {
			add_action( 'admin_notices', array( $this, 'first_time_use' ) );
		}

		// Review notice
		if( empty( get_option( 'zpm_review_notice_dismissed' ) ) && $zpm_used > 5 ) {
			//add_action( 'admin_notices', array( $this, 'review_notice' ) );
		}
		
		$this->settings->add_pages( $this->pages )->with_sub_page( __( 'Dashboard', 'zephyr-project-manager' ) )->add_sub_pages( $this->subpages )->register();
		
		add_filter( 'upload_mimes', array($this, 'custom_mime_types'), 1, 1 );
		add_action( 'admin_print_scripts', array( $this, 'hide_unrelated_notices' ) );

		add_action( 'admin_menu', array( $this, 'check_access_level' ) );
 
		add_filter('parse_query', array($this, 'filter_media_files') );
	}

	public function check_access_level() {
		$user_settings = Utillities::get_user_settings( get_current_user_id() );
		if ($user_settings['can_zephyr'] == "false") {
			remove_menu_page( 'zephyr_project_manager' );
		}
	}

	/**
	* Remove all non-Zephyr related plugin notices from plugin pages
	*/
	public function hide_unrelated_notices() {

		$zpm_pages = array(
			'zephyr_project_manager',
			'zephyr_project_manager_tasks',
			'zephyr_project_manager_files',
			'zephyr_project_manager_activity',
			'zephyr_project_manager_progress',
			'zephyr_project_manager_calendar',
			'zephyr_project_manager_settings',
			'zephyr_project_manager_projects',
			'zephyr_project_manager_categories',
			'zephyr_project_manager_teams_members',
			'zephyr_project_manager_asana',
			'zephyr_project_manager_reports',
			'zephyr_project_manager_custom_fields',
			'zephyr_project_manager_purchase_premium',
			'zephyr_project_manager_asana_settings',
			'zephyr_project_manager_devices'
		);

		// Quit if it is not on our pages
		if ( empty( $_REQUEST['page'] ) || in_array($_REQUEST['page'], $zpm_pages) === false ) {
			return;
		}

		$zpm_used = get_option('zpm_used') ? get_option('zpm_used') : 0;

		update_option('zpm_used', ($zpm_used + 1) );

		global $wp_filter;

		if ( ! empty( $wp_filter['user_admin_notices']->callbacks ) && is_array( $wp_filter['user_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['user_admin_notices']->callbacks as $priority => $hooks ) {
				
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['admin_notices']->callbacks ) && is_array( $wp_filter['admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['all_admin_notices']->callbacks ) && is_array( $wp_filter['all_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['all_admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}

	/**
	* Sets all the main plugin pages
	*/
	public static function get_pages() {
		$callbacks = new AdminCallbacks();
		$access_level = 'manage_options';
		$pages = array(
			array(
				'page_title' => __( 'Zephyr Project Manager', 'zephyr-project-manager' ), 
				'menu_title' => __( 'Zephyr Project Manager', 'zephyr-project-manager' ), 
				'capability' => $access_level, 
				'menu_slug'  => 'zephyr_project_manager', 
				'callback'   => array( $callbacks, 'adminDashboard' ), 
				'icon_url'   => ZPM_PLUGIN_URL . 'assets/img/logo.png', 
				'position'   => 110
			)
		);

		return $pages;
	}

	/**
	* Sets all the plugin subpages
	*/
	public static function get_sub_pages() {
		$callbacks = new AdminCallbacks();
		$access_level = 'manage_options';
		$subpages = array(
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Projects', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Projects', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_projects', 
				'callback'    => array( $callbacks, 'adminProjects' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Tasks', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Tasks', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_tasks', 
				'callback'    => array( $callbacks, 'adminTasks' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr File Manager', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Files', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_files', 
				'callback'    => array( $callbacks, 'adminFiles' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Activity', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Activity', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_activity', 
				'callback'    => array( $callbacks, 'adminActivity' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Calendar', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Calendar', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_calendar', 
				'callback'    => array( $callbacks, 'adminCalendar' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Categories', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Categories', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_categories', 
				'callback'    => array( $callbacks, 'adminCategories' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Devices', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Devices', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_devices', 
				'callback'    => array( $callbacks, 'devicesPage' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Settings', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Settings', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_settings', 
				'callback'    => array( $callbacks, 'adminSettings' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Teams & Members', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Teams & Members', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_teams_members', 
				'callback'    => array( $callbacks, 'adminTeamsMembers' )
			), 
			// Testing
			// array(
			// 	'parent_slug' => 'zephyr_project_manager',
			// 	'page_title'  => __( 'Gantt', 'zephyr-project-manager' ), 
			// 	'menu_title'  => __( 'Gantt', 'zephyr-project-manager' ), 
			// 	'capability'  => $access_level, 
			// 	'menu_slug'   => 'zephyr_project_manager_gantt', 
			// 	'callback'    => array( $callbacks, 'ganttPage' )
			// ), 
		);
		if (!BaseController::is_pro()) {
			$subpages[] = array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Pro', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Get Premium', 'zephyr-project-manager' ), 
				'capability'  => 'manage_options', 
				'menu_slug'   => 'zephyr_project_manager_purchase_premium', 
				'callback'    => array( $callbacks, 'purchase_premium' )
			);
		}

		return $subpages;
	}

	/**
	* Adds the custom mime types
	*/
	function custom_mime_types( $mime_types ) {
		$mime_types['json'] = 'application/json';
		return $mime_types;
	}

	/**
	* Adds the Dashboard Widgets to the Dashboard
	*/
	public function setup_dashboard_widget() {
		$project_count = Projects::project_count();
		$user_settings = Utillities::get_user_settings( get_current_user_id() );

		if ( isset( $user_settings['hide_dashboard_widgets'] ) ) {
			if ($user_settings['hide_dashboard_widgets'] == true) {
				return;
			}
		} else {
			return;
		}

		if ($user_settings['can_zephyr'] == "false") {
			return;
		}

		if ($project_count > 0) {
			wp_add_dashboard_widget(
	            'zpm_dashboard_overview',
	            __( 'Zephyr Latest Projects', 'zephyr-project-manager' ),
	            array($this, 'render_dashboard_widget' )
	        );
		}
		
		// WP Dashboard Tasks
        wp_add_dashboard_widget(
            'zpm_dashboard_tasks_overview',
            __( 'Zephyr Tasks', 'zephyr-project-manager' ),
            array($this, 'render_dashboard_tasks_widget' )
        );
	}

	/**
	* Renders the dashboard widget to display a project progress overview and the progress for the three latest projects
	*/
	public function render_dashboard_widget() {
		$project_count = Projects::project_count();
		$completed_projects = Projects::completed_project_count();
		$pending_projects = $project_count - $completed_projects;
		$latest_projects = Projects::get_projects(3);
		
		$colors = array(
			'#448AFF',
			'#7B1FA2',
			'#E91E63',
		); ?>

		<div id="zpm_dashboard_chart">
			<canvas id="zpm-dashboard-project-chart" data-project-total="<?php echo $project_count; ?>" data-project-completed="<?php echo $completed_projects; ?>" data-project-pending="<?php echo $pending_projects; ?>" width="100" height="100"></canvas>
		</div>
		<div id="zpm_project_overview">
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $project_count; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
			</span>
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $completed_projects; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Completed', 'zephyr-project-manager' ); ?></span>
			</span>
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $pending_projects; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Pending', 'zephyr-project-manager' ); ?></span>
			</span>
		</div>

		<div class="zpm_dashboard_projects">

			<?php if (sizeof( (array) $latest_projects ) > 0) : ?>
				<h3 id="zpm_dashboard_heading">Latest Projects</h3>
				<ul class="zpm_dashboard_project_list">
					<?php $i = 0; ?>
					<?php foreach ($latest_projects as $project) : ?>
						<?php $project_progress = Projects::percent_complete($project->id); ?>
						<li class="zpm_dashboard_project">
							<span class="zpm_dashboard_project_name"><?php echo $project->name; ?></span>
							<span class="zpm_dashboard_project_progress">
								<span class="zpm_dashboard_progress_bar">
									<span class="zpm_dashboard_progress_indicator zpm_color_<?php echo $i; ?>" style="width: <?php echo $project_progress . '%'; ?>; background-color: <?php echo $colors[$i] ?>"><?php echo $project_progress . '%' ?></span>
								</span>
							</span>
						</li>
						<?php $i++; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="zpm-dashboard-widget-buttons">
				<a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); ?>" class="zpm_button"><?php _e( 'View All Projects', 'zephyr-project-manager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	* Renders the dashboard widget to display a task overview of your tasks for the week and overdue tasks
	*/
	public function render_dashboard_tasks_widget() {
		$args = array(
			'limit' => 5,
			'assignee' => get_current_user_id()
		);
		$my_tasks = Tasks::get_tasks($args);
		$week_tasks = Tasks::get_week_tasks(get_current_user_id());
		$args = array( 'assignee' => get_current_user_id() );
		$overdue_tasks = Tasks::get_overdue_tasks($args); ?>

		<?php if (Tasks::get_task_count() <= 0) : ?>
			<p><?php _e( 'There are no tasks to view at the moment.', 'zephyr-project-manager' ); ?></p>
			<?php return; ?>
		<?php endif; ?>
		<h3 class="zpm_dashboard_heading"><?php _e( 'My Tasks Due This Weeks', 'zephyr-project-manager' ); ?>:</h3>
		<ul class="zpm_admin_list">
			<?php foreach($week_tasks as $task) : ?>
				<?php $due_date = date('D', strtotime($task->date_due)); ?>
				<li class="zpm-dashboard-list-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>"><?php echo stripslashes($task->name); ?><span class="zpm_widget_date zpm_date_pending"><?php echo $due_date; ?></span></a></li>
			<?php endforeach; ?>
		</ul>
		<?php if (empty($week_tasks)) : ?>
			<p><?php _e( 'You have no tasks due this week', 'zephyr-project-manager' ); ?>.</p>
		<?php endif; ?>

		<h3 class="zpm_dashboard_heading"><?php _e( 'My Overdue Tasks', 'zephyr-project-manager' ); ?>:</h3>
		<ul class="zpm_admin_list">
			<?php foreach($overdue_tasks as $task) : ?>
				<?php if ($task->date_due == '0000-00-00 00:00:00') { continue; } ?>
				<?php $due_date = date('d M', strtotime($task->date_due)); ?>
				<li class="zpm-dashboard-list-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class=""><?php echo stripslashes($task->name); ?><span class="zpm_widget_date zpm_date_overdue"><?php echo $due_date; ?></span></a></li>
			<?php endforeach; ?>
		</ul>

		<?php if (empty($overdue_tasks)) : ?>
			<p><?php _e( 'You have no overdue tasks.', 'zephyr-project-manager' ); ?></p>
		<?php endif; ?>
		
		<div class="zpm-dashboard-widget-buttons">
			<a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')); ?>" class="zpm_button"><?php _e( 'See All Tasks', 'zephyr-project-manager' ); ?></a>
		</div>

		<?php
	}

	public function render_dashboard_projects() {
		$dashboard_projects = Projects::get_dashboard_projects();
		?>
		<div class="zpm_panel_container">
		<?php
		foreach ($dashboard_projects as $dashboard_project) :
			$project = Projects::get_project($dashboard_project);
			?>
			<div class="zpm_panel_12 zpm_dashboard_project_container">
				<div class="zpm_panel zpm_chart_panel zpm_dashboard_project" data-project-id="<?php echo $project->id; ?>">
					<?php $chart_data = get_option('zpm_chart_data', array()); ?>
					<h4 class="zpm_panel_heading"><?php echo $project->name; ?></h4>
					<span class="zpm_remove_project_from_dashboard lnr lnr-cross-circle"></span>
					<canvas id="zpm_line_chart" class="zpm-dashboard-project-chart" width="600" height="350" data-project-id="<?php echo $project->id; ?>" data-chart-data='<?php echo json_encode($chart_data[$project->id]); ?>'></canvas>

				</div>
			</div>
		<?php
		endforeach;
		?>
		</div>
		<?php
	}

	/**
	* Displays the admin notice for a user to view the Zephyr page when they have not used it before
	*/
	public function first_time_use() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	        <p><?php printf( __( 'Get started with Zephyr Project Manager now from %s here %s', 'zephyr-project-manager' ), '<a href="' . esc_url(admin_url('/admin.php?page=zephyr_project_manager')) . '" class="zpm_link">', '</a>' ); ?></p>
	    </div>
	    <?php
	}

	/**
	* Displays the review notice
	*/
	public function review_notice() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	    	<span id="zpm_dismiss_review_notice" class="lnr lnr-cross-circle"></span>
	        <p><?php _e( 'Thanks for using Zephyr Project Manager. If you enjoy it, could you please consider leaving a review? It would really mean the world to me!', 'zephyr-project-manager' ); ?></p>
	        <button class="zpm_button"><a href="https://wordpress.org/support/plugin/zephyr-project-manager/reviews/" target="_blank"><?php _e( 'Leave a Review', 'zephyr-project-manager' ); ?></a></button>
	    </div>
	    <?php
	}

	/**
	* Displays the welcome notice
	*/
	public function welcome_notice() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	    	<span id="zpm_dismiss_welcome_notice" class="lnr lnr-cross-circle"></span>
	        <h4 class="zpm_notice_heading"><?php _e('Welcome to Zephyr Project Manager', 'zephyr-project-manager'); ?></h4>
			<p class="zpm_panel_description">
				<?php _e('Thanks for using Zephyr Project Manager. If should experience any problems or have any feature requests, I would be more than happy to add them. Please contact me at dylanjkotze@gmail.com for any queries.', 'zephyr-project-manager') ?>
			</p>
	    </div>
	    <?php
	}

	public function filter_media_files( $wp_query ) {
	    // if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
	    //     global $current_user;
	    //         $wp_query->set( 'author', $current_user->id );
	    // }
	}
}