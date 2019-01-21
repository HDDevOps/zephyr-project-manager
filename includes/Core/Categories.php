<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Base\BaseController;
use Inc\Api\ColorPickerApi;

class Categories {

	/**
	* Creates a new category
	*/
	public static function create( $args ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array();
		$settings['name'] = (isset($args['name'])) ? sanitize_text_field( $args['name']) : 'Untitled';
		$settings['description'] = (isset($args['description'])) ? sanitize_text_field( $args['description']) : '';
		$settings['color'] 	= (isset($args['color'])) ? sanitize_text_field( $args['color']) : false;

		if ( ColorPickerApi::checkColor( $settings['color'] ) !== false ) {
			$settings['color'] = ColorPickerApi::sanitizeColor( $settings['color'] );
		} else {
			$settings['color'] = '#eee';
		}

		$wpdb->insert( $table_name, $settings );
		return $wpdb->insert_id;
	}

	/**
	* Updates category
	*/
	public static function update( $id, $args ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		if (isset($args['color'])) {
			if ( ColorPickerApi::checkColor( $args['color'] ) !== false ) {
				$args['color'] = ColorPickerApi::sanitizeColor( $args['color'] );
			} else {
				$args['color'] = '#eee';
			}
		}

		$where = array(
			'id' => $id
		);

		$wpdb->update( $table_name, $args, $where );
		return $args;
	}

	/**
	* Deletes a category
	*/
	public static function delete( $id ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;

		$settings = array(
			'id' => $id
		);

		$wpdb->delete( $table_name, $settings, [ '%d' ] );
	}

	/**
	* Retrieves all categories
	* @return object
	*/
	public static function get_categories() {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$query = "SELECT * FROM $table_name";
		$categories = $wpdb->get_results($query);
		return $categories;
	}

	/**
	* Retrieves the data for a category
	* @param int $id The ID of the category to retrieve the data for
	* @return object
	*/
	public static function get_category( $id ) {
		global $wpdb;
		$table_name = ZPM_CATEGORY_TABLE;
		$query = "SELECT * FROM $table_name WHERE id = $id";
		$category = $wpdb->get_row($query);
		return $category;
	}

	/**
	* Returns the total number of categories
	* @return int
	*/
	public static function get_category_total() {
		$categories = Categories::get_categories();
		$category_count = sizeof($categories);
		return $category_count;
	}

	/**
	* Displays a list of created categories
	*/
	public static function display_category_list() {
		return require_once( ZPM_PLUGIN_PATH . '/templates/parts/category_list.php' );
	}

	public static function new_category_modal() {
		?>
		<!-- New Category modal -->
		<div id="zpm_new_category_modal" class="zpm-modal">
			<div class="zpm_create_category">
				<h3 class="zpm-modal-header"><?php _e( 'New Category', 'zephyr-project-manager' ); ?></h3>
				<label class="zpm_label" for="zpm_category_name"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
				<input type="text" id="zpm_category_name" class="zpm_input" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>">
				<label class="zpm_label" for="zpm_category_description"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
				<textarea type="text" id="zpm_category_description" class="zpm_input" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"></textarea>
				<label class="zpm_label" for="zpm_category_color"><?php _e( 'Color', 'zephyr-project-manager' ); ?></label>
				<input type="text" id="zpm_category_color" class="zpm_input">
			</div>
			<button class="zpm_button" name="zpm_create_category" id="zpm_create_category"><?php _e( 'Create Category', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
	}
}