<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Base;
if ( !defined( 'ABSPATH' ) ) {
	die;
}

class Activate {
	public static function activate(){
		global $wpdb;
		flush_rewrite_rules();
		$table_name = ZPM_PROJECTS_TABLE;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				name text NOT NULL,
				description text NOT NULL,
				completed boolean NOT NULL,
				team varchar(100) NOT NULL,
				categories varchar(100) NOT NULL,
				status varchar(255) NOT NULL,
				date_created TIMESTAMP NOT NULL,
				date_due TIMESTAMP NOT NULL,
				date_start TIMESTAMP NOT NULL,
				date_completed TIMESTAMP NOT NULL,
				other_data varchar(999) NOT NULL,
				other_settings varchar(999) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = ZPM_TASKS_TABLE;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  parent_id mediumint(9) NOT NULL DEFAULT '-1',
			  user_id mediumint(9) NOT NULL,
			  project mediumint(9) NOT NULL,
			  assignee mediumint(9) NOT NULL,
			  name text NOT NULL,
			  description text NOT NULL,
			  categories varchar(100) NOT NULL,
			  completed boolean NOT NULL,
			  date_created TIMESTAMP NOT NULL,
			  date_start TIMESTAMP NOT NULL,
			  date_due TIMESTAMP NOT NULL,
			  date_completed TIMESTAMP NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = ZPM_CATEGORY_TABLE;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name text NOT NULL,
			  description text NOT NULL,
			  color text NOT NULL,
			  UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = ZPM_MESSAGES_TABLE;
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				subject VARCHAR(255) NOT NULL,
				subject_id mediumint(9) NOT NULL,
				parent_id mediumint(9) NOT NULL,
				message text NOT NULL,
				type VARCHAR(255) NOT NULL,
				date_created TIMESTAMP NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = ZPM_ACTIVITY_TABLE;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id mediumint(9) NOT NULL,
				subject_id mediumint(9) NOT NULL,
				subject_name text NOT NULL,
				old_name text NOT NULL,
				subject text NOT NULL,
				action text NOT NULL,
				date_done TIMESTAMP NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = ZPM_PROJECTS_TABLE;
		$status = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'status'");

		if (empty($status)) {
			$wpdb->query("ALTER TABLE $table_name ADD status varchar(255) NOT NULL");
		}
		
		$status = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'other_settings'");

		if (empty($status)) {
			$wpdb->query("ALTER TABLE $table_name ADD other_settings varchar(999) NOT NULL");
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			if (empty($wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'type'"))) {
				$wpdb->query("ALTER TABLE $table_name ADD type varchar(255)");
			}
		}

		$table_name = ZPM_TASKS_TABLE;
		$team = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'team'");

		if (empty($team)) {
			$wpdb->query("ALTER TABLE $table_name ADD team TEXT NOT NULL");
		}

		$table_name = ZPM_TASKS_TABLE;
		$result = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'team'");
		$exists = !empty($result) ? true : false;

		if (!$exists) {
			echo 'adding team';
			$wpdb->query("ALTER TABLE $table_name ADD team TEXT NOT NULL");
		}
	}
}