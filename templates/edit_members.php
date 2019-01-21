<?php
	use Inc\Core\Utillities;

	$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

	$current_user = get_user_by( 'ID', $user_id );
	$user_id = $current_user->data->ID;
	$user_name = $current_user->data->display_name;
	$user_email = $current_user->data->user_email;

	// Save Profile Settings
	if (isset($_POST['zpm_profile_settings'])) {
		check_admin_referer('zpm_save_project_settings');

		$name = (isset($_POST['zpm_settings_name']) && $_POST['zpm_settings_name'] !== '') ? sanitize_text_field($_POST['zpm_settings_name']) : $user_name;
		$profile_picture = isset($_POST['zpm_profile_picture']) ? sanitize_text_field($_POST['zpm_profile_picture']) : get_avatar_url($user_id);
		$description = isset($_POST['zpm_settings_description']) ? sanitize_textarea_field($_POST['zpm_settings_description']) : '';
		$email = (isset($_POST['zpm_settings_email']) && $_POST['zpm_settings_email'] !== '') ? sanitize_email($_POST['zpm_settings_email']) : $user_email;
		$notify_activity = isset($_POST['zpm_notify_activity']) ? 1 : '0';
		$can_zephyr = isset($_POST['zpm_can_zephyr']) ? "true" : "false";
		$notify_tasks = isset($_POST['zpm_notify_tasks']) ? 1 : '0';
		$notify_updates = isset($_POST['zpm_notify_updates'] ) ? 1 : '0';
		$notify_task_assigned = isset($_POST['zpm_notify_task_assigned'] ) ? '1' : '0';
		$access_level = isset($_POST['zpm-access-level']) ? $_POST['zpm-access-level'] : 'edit_posts';
		
		$settings = array(
			'user_id' 		  => $user_id,
			'profile_picture' => $profile_picture,
			'name' 			  => $name,
			'description' 	  => $description,
			'email' 		  => $email,
			'notify_activity' => $notify_activity,
			'notify_tasks' 	  => $notify_tasks,
			'notify_updates'  => $notify_updates,
			'notify_task_assigned' => $notify_task_assigned,
			'can_zephyr'	  => $can_zephyr
		);
		update_option( 'zpm_user_' . $user_id . '_settings', $settings );
		update_option( 'zpm_access_settings', $access_level );
	}

	$user_settings_option = get_option('zpm_user_' . $user_id . '_settings');
	$general_settings = Utillities::general_settings();
	$access_settings = get_option('zpm_access_settings');
	$settings_profile_picture = (isset($user_settings_option['profile_picture'])) ? esc_url($user_settings_option['profile_picture']) : esc_url(get_avatar_url($user_id));
	$settings_name = (isset($user_settings_option['name'])) ? esc_html($user_settings_option['name']) : esc_html($user_name);
	$can_zephyr = isset($user_settings_option['can_zephyr']) ? $user_settings_option['can_zephyr'] : "true";
	$access_level = $access_settings ? $access_settings : 'edit_posts';
	$settings_description = isset($user_settings_option['description']) ? esc_textarea($user_settings_option['description']) : '';
	$settings_email = isset($user_settings_option['email']) ? esc_html($user_settings_option['email']) : esc_html($user_email);
	$settings_notify_activity = (isset($user_settings_option['notify_activity'])) ? $user_settings_option['notify_activity'] : '0';
	$settings_notify_tasks = (isset($user_settings_option['notify_tasks'])) ? $user_settings_option['notify_tasks'] : '1';
	$settings_notify_updates = (isset($user_settings_option['notify_updates'])) ? $user_settings_option['notify_updates'] : '0';
	$settings_notify_task_assigned = (isset($user_settings_option['notify_task_assigned'])) ? $user_settings_option['notify_task_assigned'] : '1';
	$settings_notifications['activity'] = $settings_notify_activity == '1' ? esc_attr('checked') : '';
	$settings_notifications['tasks'] = $settings_notify_tasks == '1' ? esc_attr('checked') : '';
	$settings_notifications['updates'] = $settings_notify_updates == '1' ? esc_attr('checked') : '';
	$settings_notifications['task_assigned'] = $settings_notify_task_assigned == '1' ? esc_attr('checked') : '';
	?>
	

	<h1 class="zpm_page_title">
		<a class="zpm-back-link" href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_teams_members')); ?>"><i class="dashicons dashicons-arrow-left-alt2"></i></a> <?php _e( 'Edit Member', 'zephyr-project-manager' ); ?> - <?php echo $settings_name; ?></h1>
		<!-- Profile Settings -->
		<div id="zpm-edit-member-container" class="zpm_body">
			<form id="zpm_profile_settings" method="post">
				<label class="zpm_label"><?php _e( 'Profile Picture', 'zephyr-project-manager' ); ?></label>
				<div class="zpm_settings_profile_picture">
					<span class="zpm_settings_profile_background"></span>
					<span class="zpm_settings_profile_image" style="background-image: url(<?php echo $settings_profile_picture; ?>);"></span>
				</div>

				<input type="hidden" id="zpm_profile_picture_hidden" name="zpm_profile_picture" value="<?php echo $settings_profile_picture; ?>" />
				<input type="hidden" id="zpm_gravatar" value="<?php echo get_avatar_url($user_id); ?>" />

				<label class="zpm_label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				<input type="text" class="zpm_input" name="zpm_settings_name" value="<?php echo $settings_name; ?>" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>" />

				<label class="zpm_label"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
					<textarea name="zpm_settings_description" class="zpm_input" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"><?php echo $settings_description; ?></textarea>

				<label class="zpm_label"><?php _e( 'Email Address', 'zephyr-project-manager' ); ?></label>
				<input type="text" class="zpm_input" name="zpm_settings_email" value="<?php echo $settings_email; ?>" placeholder="<?php _e( 'Email Address', 'zephyr-project-manager' ); ?>"/>

				<label class="zpm_label"><?php _e( 'Email Notifications', 'zephyr-project-manager' ); ?></label>
					<div class="zpm_settings_notification">
						<label for="zpm_notifications_activity" class="zpm_checkbox_label">
							<input type="checkbox" id="zpm_notifications_activity" name="zpm_notify_activity" class="zpm_project_edit_categories zpm_toggle invisible" value="1" <?php echo $settings_notifications['activity']; ?>>
							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'All Activity', 'zephyr-project-manager' ); ?>
					    </label>
					</div>

					<div class="zpm_settings_notification">
						<label for="zpm_notifications_reminders" class="zpm_checkbox_label">
							<input type="checkbox" id="zpm_notifications_reminders" name="zpm_notify_tasks" class="zpm_project_edit_categories zpm_toggle invisible" value="1" <?php echo $settings_notifications['tasks']; ?>>
							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'Task Reminders', 'zephyr-project-manager' ); ?>
					    </label>
					</div>

					<div class="zpm_settings_notification">
						<label for="zpm_notifications_task_assigned" class="zpm_checkbox_label">
							<input type="checkbox" id="zpm_notifications_task_assigned" name="zpm_notify_task_assigned" class="zpm_project_edit_categories zpm_toggle invisible" value="1" <?php echo $settings_notifications['task_assigned']; ?>>
							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'Tasks Assigned', 'zephyr-project-manager' ); ?>
					    </label>
					</div>

					<div class="zpm_settings_notification">
						<label for="zpm_notifications_updates" class="zpm_checkbox_label">
							<input type="checkbox" id="zpm_notifications_updates" name="zpm_notify_updates" class="zpm_project_edit_categories zpm_toggle invisible" value="1" <?php echo $settings_notifications['updates']; ?>>
							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'Weekly Updates', 'zephyr-project-manager' ); ?>
					    </label>
					</div>

					<?php 
						if (current_user_can('administrator')) :
					?>
					<label class="zpm_label"><?php _e( 'Allow access to Zephyr', 'zephyr-project-manager' ); ?></label>
					<div class="zpm_settings_notification">
						<label for="zpm-can-zephyr-<?php echo $user_id; ?>" class="zpm_checkbox_label">

							<input type="checkbox" id="zpm-can-zephyr-<?php echo $user_id; ?>" name="zpm_can_zephyr" class="zpm-can-zephyr zpm_toggle invisible" value="1" data-user-id="<?php echo $user->data->ID; ?>" <?php echo $can_zephyr == "true" ? 'checked' : ''; ?>>

							<div class="zpm_main_checkbox">
								<svg width="20px" height="20px" viewBox="0 0 20 20">
									<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
									<polyline points="4 11 8 15 16 6"></polyline>
								</svg>
							</div>
							<?php _e( 'Allow Access', 'zephyr-project-manager' ); ?>
					    </label>
					</div>

					<label class="zpm_label"><?php _e( 'Lowest Level of Access to Zephyr Project Manager', 'zephyr-project-manager' ); ?></label>
					<select id="zpm-access-level" class="zpm_input" name="zpm-access-level">
							<option value="manage_options" <?php echo $access_level == 'manage_options' ? 'selected' : ''; ?>><?php _e( 'Administrator', 'zephyr-project-manager' ); ?></option>
							<option value="edit_pages" <?php echo $access_level == 'edit_pages' ? 'selected' : ''; ?>><?php _e( 'Editor', 'zephyr-project-manager' ); ?></option>
							<option value="edit_published_posts" <?php echo $access_level == 'edit_published_posts' ? 'selected' : ''; ?>><?php _e( 'Author', 'zephyr-project-manager' ); ?></option>
							<option value="edit_posts" <?php echo $access_level == 'edit_posts' ? 'selected' : ''; ?>><?php _e( 'Contributor', 'zephyr-project-manager' ); ?></option>
							<option value="read" <?php echo $access_level == 'read' ? 'selected' : ''; ?>><?php _e( 'Subscriber', 'zephyr-project-manager' ); ?></option>
					</select>
					<?php
						endif;
					?>
					<?php wp_nonce_field('zpm_save_project_settings'); ?>
				<button type="submit" class="zpm_button" name="zpm_profile_settings" id="zpm_profile_settings"><?php _e( 'Save Settings', 'zephyr-project-manager' ); ?></button>
			</form>
		</div>