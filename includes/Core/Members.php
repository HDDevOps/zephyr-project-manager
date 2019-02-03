<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use \DateTime;
use Inc\Core\Utillities;
use Inc\Base\BaseController;

class Members {
	public static function get_teams() {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );

		foreach ( $teams as $team_key => $team ) {
			foreach ( $team['members'] as $key => $value ) {
				$teams[$team_key]['members'][$key] = Utillities::get_user_settings( $value );
			}
		}

		return (array) $teams;
	}

	public static function get_team( $id ) {
		$teams = Members::get_teams();

		foreach ( $teams as $team ) {
			if ( $team['id'] == $id ) {
				return $team;
			}
		}

		return;
	}

	public static function add_team( $name, $description, $members ) {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );

		$last_team = end( $teams );
		$id = !empty( $last_team ) ? (int) $last_team['id'] + 1 : '0';

		$new_team = array(
			'id' 		  => $id,
			'name' 		  => $name,
			'description' => $description,
			'members' 	  => $members
		);

		reset( $teams );
		$teams[] = $new_team;
		update_option( 'zpm_teams', serialize( $teams ) );
		return $id;
	}

	public static function update_team( $id, $name, $description, $members ) {
		$teams = maybe_unserialize( get_option( 'zpm_teams', array() ) );
		
		foreach ($teams as $key => $value) {
			if ( $value['id'] == $id ) {
				$update_team = array(
					'id' 		  => $id,
					'name' 		  => $name,
					'description' => $description,
					'members' 	  => $members
				);
				$teams[$key] = $update_team;
			}
		}

		update_option( 'zpm_teams', serialize( $teams ) );
	}

	public static function delete_team( $id ) {
		$teams = Members::get_teams();

		foreach ($teams as $key => $value) {
			if ( $value['id'] == $id ) {
				unset($teams[$key]);
			}
		}

		update_option( 'zpm_teams', serialize( $teams ) );
	}

	public static function get_members() {
		$users = get_users();
		$members = array();

		foreach ($users as $user) {
			$settings = Utillities::get_user_settings( $user->ID );
			$members[] = $settings;
		}

		return $members;
	}

	public static function get_member_name( $member_id ) {
		$member = Utillities::get_user_settings( $member_id );
		return isset( $member['name'] ) ? $member['name'] : "";
	}

	/**
	* Returns an array of all users that have access to Zephyr
	*/
	public static function get_zephyr_members() {
		$users = get_users();
		$members = array();

		foreach ($users as $user) {
			
			$settings = Utillities::get_user_settings( $user->ID );

			if (( isset( $settings['can_zephyr'] ) && $settings['can_zephyr'] == "true" ) || !isset( $settings['can_zephyr'] )) {
				$members[] = $settings;
			}
		}

		return $members;
	}

	public static function team_single_html( $team ) {
		ob_start();
		?>
		<div class="zpm_team_member" data-team-id="<?php echo $team['id']; ?>">
			<div class="zpm_member_details" data-ripple="rgba(0,0,0,0.1)" zpm-ripple>
				<h3 class="zpm-team-name"><?php echo $team['name']; ?></h3>
				<p class="zpm-description-text"><?php echo $team['description'] !== "" ? $team['description'] : "<p class='zpm-no-description-error'>No description added.</p>"; ?></p>
				<h3 class="zpm-team-members-title"><?php _e( 'Members', 'zephyr-project-manager' ); ?></h3>

				<ul class="zpm-team-member-list">
					<?php $member_count = 0; ?>
					<?php foreach ($team['members'] as $member) : ?>
						<?php if (!isset($member['name'])) { continue; } ?>
						<li><?php echo $member['name']; ?></li>
						<?php $member_count++; ?>
					<?php endforeach; ?>
				</ul>

				<?php if ($member_count <= 0) : ?>
					<p class="zpm-team-no-members"><?php _e( 'No members have been added to this team', 'zephyr-project-manager' ); ?></p>
				<?php endif; ?>

				<div class="zpm-team-options-btns">
					<button class="zpm_button zpm-delete-team" data-team-id="<?php echo $team['id']; ?>">Delete</button>
					<button class="zpm_button zpm-edit-team" data-team-id="<?php echo $team['id']; ?>" data-zpm-modal-trigger="zpm-edit-team-modal"><?php _e( 'Edit Team', 'zephyr-project-manager' ); ?></button>
				</div>
			</div>
		</div>
		<?php

		$html = ob_get_clean();
		return $html;
	}

	public static function team_dropdown_html( $id = '', $selected = null) {
		ob_start();

		$teams = Members::get_teams();
		
		?>
			<select id="<?php echo $id !== '' ? $id : 'zpm-team-select-dropdown'; ?>" class="zpm-team-select-dropdown zpm_input zpm-input-chosen">
				<option value="-1"><?php _e( 'Select Team', 'zephyr-project-manager' ); ?></option>
				<?php foreach ($teams as $team) : ?>
					<option value="<?php echo $team['id']; ?>" <?php echo !is_null( $selected ) && $selected == $team['id'] ? 'selected' : ''; ?>><?php echo $team['name']; ?></option>
				<?php endforeach; ?>
			</select>
		<?php

		$html = ob_get_clean();
		return $html;
	}
}