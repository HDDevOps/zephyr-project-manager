<?php
	/**
	* Category Page
	* Page for creating, editing, viewing and delelting categories
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Categories;
?>

<!-- Category List -->
<div class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container" class="zpm_add_task">
		<div class="zpm_body zpm_category_display">
			<button id="zpm_new_category_btn" class="zpm_button"><?php _e( 'New Category', 'zephyr-project-manager' ); ?></button>
			<h3><?php _e( 'Categories', 'zephyr-project-manager' ); ?></h3>
			<div class="zpm_category_list">
				<?php Categories::display_category_list(); ?>
			</div>
		</div>
	</div>
</div>

<!-- Edit Category modal -->
<div id="zpm_edit_category_modal" class="zpm-modal">
	<div class="zpm_edit_category" data-category-id="">
	
		<div class="zpm-form__group">
			<input type="text" name="zpm_edit_category_name" id="zpm_edit_category_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>">
			<label for="zpm_edit_category_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
		</div>

		<div class="zpm-form__group">
			<textarea type="text" name="zpm_edit_category_description" id="zpm_edit_category_description" class="zpm-form__field" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"></textarea>
			<label for="zpm_edit_category_description" class="zpm-form__label"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
		</div>
		
		<label class="zpm_label" for="zpm_edit_category_color"><?php _e( 'Color', 'zephyr-project-manager' ); ?></label>
		<input type="text" id="zpm_edit_category_color" class="zpm_input">
	</div>
	<button class="zpm_button" name="zpm_edit_category" id="zpm_edit_category"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
</div>
<?php $this->get_footer(); ?>