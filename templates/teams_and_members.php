<?php
	/*
	* Teams & Members Page
	* This page is used to display and manage teams and team members as well as add and remove them
	*/
	
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;
	use Inc\Core\Utillities;
	use Inc\Core\Members;

	$Projects = new Projects;
	$base = new BaseController;
	$users = $base->get_users();
	$all_members = Members::get_zephyr_members();
?>

<main class="zpm_settings_wrap">

	<?php $this->get_header(); ?>

	<div id="zpm_container">
		
		<div class="zpm_body zpm_body_no_background">
			<?php if (isset($_GET['action']) && $_GET['action'] == 'edit_member') : ?>
				<?php include('edit_members.php'); ?>
			<?php else: ?>
				<h1 class="zpm_page_title"><?php _e( 'Members', 'zephyr-project-manager' ); ?></h1>
				<div id="zpm_members">
					
					<?php foreach ($users as $user) : ?>
						<?php
							$role = '';
							$user_id = $user->data->ID;
							$user_settings_option = get_option('zpm_user_' . $user->data->ID . '_settings');
							$avatar = isset($user_settings_option['profile_picture']) ? esc_url($user_settings_option['profile_picture']) : get_avatar_url($user->data->ID);
							$can_zephyr = isset($user_settings_option['can_zephyr']) ? $user_settings_option['can_zephyr'] : "true";

							$description = isset($user_settings_option['description']) ? esc_html($user_settings_option['description']) : '';

							$user_projects = $Projects->get_user_projects($user->data->ID);
							$user_tasks = Tasks::get_user_tasks($user->data->ID);
							$completed_tasks = Tasks::get_user_completed_tasks($user->data->ID);
							$remaining_tasks = Tasks::get_user_completed_tasks($user->data->ID, '0');

							$percent_complete = (sizeof($user_tasks) !== 0) ? (sizeof($completed_tasks) / sizeof($user_tasks)) * 100 : '0';

							if (in_array('zpm_user', $user->roles)) {
								$role = 'ZPM User';
							} elseif (in_array('zpm_client_user', $user->roles)) {
								$role = 'ZPM Client User';
							} elseif (in_array('zpm_manager', $user->roles) || in_array('administrator', $user->roles)) {
								$role = 'ZPM Manager';
							}
						?>

						<?php $edit_url = esc_url(admin_url('/admin.php?page=zephyr_project_manager_teams_members')) . '&action=edit_member&user_id=' . $user->data->ID; ?>

						<a class="zpm_team_member <?php echo $can_zephyr == "true" ? 'zpm-user-can-zephyr' : ''; ?>" <?php echo current_user_can( 'administrator' ) ? "href='" . $edit_url . "'" : ''; ?>>
							<div class="zpm_member_details" data-ripple="rgba(0,0,0,0.1)">
								
								<span class="zpm_avatar_image" style="background-image: url(<?php echo $avatar; ?>);"></span>
								<span class="zpm_member_name"><?php echo $user->data->display_name; ?></span>
								<span class="zpm_member_email"><?php echo $user->data->user_email; ?></span>
								<p class="zpm_member_bio"><?php echo $description; ?></p>

								<?php if (current_user_can('administrator')) : ?>
									<!-- Adcurrent_user_can('administrator')min Controls -->
									<div class="zpm-access-controls">
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
								<?php endif; ?>

								<div class="zpm_member_stats">
									<div class="zpm_member_stat">
										<h5 class="zpm_member_stat_number"><?php echo sizeof($user_projects); ?></h5>
										<p class="zpm_member_stat_label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></p>
									</div>
									<div class="zpm_member_stat">
										<h5 class="zpm_member_stat_number"><?php echo sizeof($completed_tasks); ?></h5>
										<p class="zpm_member_stat_label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
									</div>
									<div class="zpm_member_stat">
										<h5 class="zpm_member_stat_number"><?php echo sizeof($remaining_tasks); ?></h5>
										<p class="zpm_member_stat_label"><?php _e( 'Remaining Tasks', 'zephyr-project-manager' ); ?></p>
									</div>
									<div class="zpm_member_progress">
										<span class="zpm_member_progress_bar" style="width: <?php echo $percent_complete; ?>%"></span>
									</div>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

				<h1 class="zpm_page_title"><?php _e( 'Teams', 'zephyr-project-manager' ); ?> <button id="zpm-create-team-btn" class="zpm_button" data-zpm-modal-trigger="zpm-new-team-modal"><?php _e( 'New Team', 'zephyr-project-manager' ); ?></button></h1>

				<?php
					$teams = Members::get_teams();
				?>
				<div class="zpm-teams-list" id="zpm_members">

					<?php foreach ($teams as $team) : ?>
						<?php echo Members::team_single_html( $team ) ?>
					<?php endforeach; ?>
					<?php if (sizeof( $teams ) <= 0) : ?>
						<p id="zpm-no-teams-notice" class="zpm-no-results-error"><?php _e( 'There are no teams yet...', 'zephyr-project-manager' ); ?></p>
					<?php endif; ?>
				</div>

			<?php endif; ?>
		</div>
	</div>
</main>

<div id="zpm-new-team-modal" class="zpm-modal">
	<h3 class="zpm-modal-title"><?php _e( 'New Team', 'zephyr-project-manager' ); ?></h3>

	<div class="zpm-form__group">
		<input type="text" name="zpm-new-team-name" id="zpm-new-team-name" class="zpm-form__field" placeholder="<?php _e( 'Team Name', 'zephyr-project-manager' ); ?>">
		<label for="zpm-new-team-name" class="zpm-form__label"><?php _e( 'Team Name', 'zephyr-project-manager' ); ?></label>
	</div>

	<div class="zpm-form__group">
		<textarea type="text" name="zpm-new-team-description" id="zpm-new-team-description" class="zpm-form__field" placeholder="<?php _e( 'Team Description', 'zephyr-project-manager' ); ?>"></textarea>
		<label for="zpm-new-team-description" class="zpm-form__label"><?php _e( 'Team Description', 'zephyr-project-manager' ); ?></label>
	</div>

	<ul class="zpm-new-team-member-list">
		<?php foreach ($all_members as $member) : ?>
			<?php if(!isset($member['id']) || !isset($member['name'])) { continue; } ?>
			<li>
				<span class="zpm-memeber-toggle">
					<input type="checkbox" id="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-new-team-member" data-member-id="<?php echo isset($member['id']) ? $member['id'] : '';; ?>" >
					<label for="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
					</label>
				</span>
				<?php echo $member['name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="zpm-buttons-right">
		<button id="zpm-new-team" class="zpm_button"><?php _e( 'Create Team', 'zephyr-project-manager' ); ?></button>
	</div>
</div>

<div id="zpm-edit-team-modal" class="zpm-modal">

	<div id="zpm-modal-loader-edit-team" class="zpm-modal-preloader">
		<div class="zpm-loader-holder"><div class="zpm-loader"></div></div>
	</div>

	<h3 class="zpm-modal-title"><?php _e( 'Edit Team', 'zephyr-project-manager' ); ?></h3>

	<div class="zpm-form__group">
		<input type="text" name="zpm-edit-team-name" id="zpm-edit-team-name" class="zpm-form__field" placeholder="<?php _e( 'Team Name', 'zephyr-project-manager' ); ?>">
		<label for="zpm-edit-team-name" class="zpm-form__label"><?php _e( 'Team Name', 'zephyr-project-manager' ); ?></label>
	</div>

	<div class="zpm-form__group">
		<input type="text" name="zpm-edit-team-description" id="zpm-edit-team-description" class="zpm-form__field" placeholder="<?php _e( 'Team Description', 'zephyr-project-manager' ); ?>">
		<label for="zpm-edit-team-description" class="zpm-form__label"><?php _e( 'Team Description', 'zephyr-project-manager' ); ?></label>
	</div>

	<input type="hidden" id="zpm-edit-team-id" />

	<ul class="zpm-edit-team-member-list">
		<?php foreach ($all_members as $member) : ?>
			<li>
				<span class="zpm-memeber-toggle">
					<input type="checkbox" id="<?php echo 'zpm-member-edit-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-edit-team-member" data-member-id="<?php echo $member['id']; ?>">
					<label for="<?php echo 'zpm-member-edit-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
					</label>
				</span>
				<?php echo $member['name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="zpm-buttons-right">
		<button id="zpm-edit-team" class="zpm_button"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
	</div>
</div>
<?php $this->get_footer(); ?>