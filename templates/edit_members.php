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

				<div class="zpm-form__group">
					<input type="text" name="zpm_settings_name" id="zpm_settings_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>" value="<?php echo $settings_name; ?>">
					<label for="zpm_settings_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				</div>

				<div class="zpm-form__group">
					<textarea type="text" name="zpm_settings_description" id="zpm_settings_description" class="zpm-form__field" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"><?php echo $settings_description; ?></textarea>
					<label for="zpm_settings_description" class="zpm-form__label"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
				</div>

				<div class="zpm-form__group">
					<input type="text" name="zpm_settings_email" id="zpm_settings_email" class="zpm-form__field" placeholder="<?php _e( 'Email Address', 'zephyr-project-manager' ); ?>" value="<?php echo $settings_email; ?>">
					<label for="zpm_settings_email" class="zpm-form__label"><?php _e( 'Email Address', 'zephyr-project-manager' ); ?></label>
				</div>

				<label class="zpm_label"><?php _e( 'Hide WordPress Dashboard Widgets', 'zephyr-project-manager' ); ?></label>

				<label for="zpm-hide-dashboard-widgets" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm-hide-dashboard-widgets" name="zpm-hide-dashboard-widgets" class="zpm_toggle invisible" value="1" <?php echo isset( $settings['hide_dashboard_widgets'] ) && $settings['hide_dashboard_widgets'] == true ? 'checked' : '';  ?>>
					<span class="zpm-material-checkbox-label"><?php _e( 'Hidden', 'zephyr-project-manager' ); ?></span>
				</label>

				<label class="zpm_label"><?php _e( 'Email Notifications', 'zephyr-project-manager' ); ?></label>

				<label for="zpm_notify_activity" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_notify_activity" name="zpm_notify_activity" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['activity']; ?> >
					<span class="zpm-material-checkbox-label"><?php _e( 'All Activity', 'zephyr-project-manager' ); ?></span>
				</label>

				<label for="zpm_notify_tasks" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_notify_tasks" name="zpm_notify_tasks" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['tasks']; ?> >
					<span class="zpm-material-checkbox-label"><?php _e( 'Task Reminders', 'zephyr-project-manager' ); ?></span>
				</label>

				<label for="zpm_notify_task_assigned" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_notify_task_assigned" name="zpm_notify_task_assigned" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['task_assigned']; ?> >
					<span class="zpm-material-checkbox-label"><?php _e( 'Task Assigned', 'zephyr-project-manager' ); ?></span>
				</label>

				<label for="zpm_notify_updates" class="zpm-material-checkbox">
					<input type="checkbox" id="zpm_notify_updates" name="zpm_notify_updates" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['updates']; ?> >
					<span class="zpm-material-checkbox-label"><?php _e( 'Weekly Updates', 'zephyr-project-manager' ); ?></span>
				</label>

					<?php 
						if (current_user_can('administrator')) :
					?>
					<label class="zpm_label"><?php _e( 'Allow access to Zephyr', 'zephyr-project-manager' ); ?></label>
					<label for="zpm-can-zephyr-<?php echo $user_id; ?>" class="zpm-material-checkbox">
							<input type="checkbox" id="zpm-can-zephyr-<?php echo $user_id; ?>" name="zpm_can_zephyr" class="zpm_toggle invisible" data-user-id="<?php echo $user->data->ID; ?>" <?php echo $can_zephyr == "true" ? 'checked' : ''; ?> value="1">
							<span class="zpm-material-checkbox-label"><?php _e( 'Allow Access', 'zephyr-project-manager' ); ?></span>
						</label>

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