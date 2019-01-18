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
			<button id="zpm_new_category_btn" class="zpm_button">New Category</button>
			<h3>Categories</h3>
			<div class="zpm_category_list">
				<?php Categories::display_category_list(); ?>
			</div>
		</div>
	</div>
</div>

<!-- Edit Category modal -->
<div id="zpm_edit_category_modal" class="zpm-modal">
	<div class="zpm_edit_category" data-category-id="">
		<label class="zpm_label" for="zpm_edit_category_name">Name</label>
		<input type="text" id="zpm_edit_category_name" class="zpm_input">
		<label class="zpm_label" for="zpm_edit_category_description">Description</label>
		<textarea type="text" id="zpm_edit_category_description" class="zpm_input"></textarea>
		<label class="zpm_label" for="zpm_edit_category_color">Color</label>
		<input type="text" id="zpm_edit_category_color" class="zpm_input">
	</div>
	<button class="zpm_button" name="zpm_edit_category" id="zpm_edit_category">Save Changes</button>
</div>
<?php $this->get_footer(); ?>