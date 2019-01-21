<?php 
/**
* Template for displaying the footer of the Zephyr Project Manager pages
*/
if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Categories;
use Inc\Base\BaseController;
use Inc\ZephyrProjectManager\CustomFields;

?>

<div id="zpm_new_file_upload" class="zpm-modal">
	<h3 class="zpm_modal_header"><?php _e( 'New Attachment', 'zephyr-project-manager' ); ?></h3>
	<input type="hidden" id="zpm_uploaded_file_name">
	<label class="zpm_label"><?php _e( 'Project', 'zephyr-project-manager' ); ?></label>
	<?php Projects::project_select('zpm_file_upload_project'); ?>
	<div class="zpm_modal_footer">
		<button id="zpm_upload_file" class="zpm_button"><?php _e( 'Select File', 'zephyr-project-manager' ); ?></button>
		<button id="zpm_submit_file" class="zpm_button"><?php _e( 'Upload Attachment', 'zephyr-project-manager' ); ?></button>
	</div>
</div>
<?php Tasks::new_task_modal(); ?>
<?php Tasks::view_container(); ?>
<?php Projects::project_modal(); ?>
<?php Categories::new_category_modal(); ?>

<?php if (BaseController::is_pro()) : ?>
	<?php CustomFields::task_custom_fields(); ?>
<?php endif; ?>