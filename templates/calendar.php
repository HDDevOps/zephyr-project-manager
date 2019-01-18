<?php 
	/**
	* This is the page for displaying the calender and task dates 
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<main class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<article class="zpm_body">
			<h3>Calender</h3>
			<div class="zpm_body_panel">
				<div id="zpm_calendar">
					<div class="zpm_task_loader"></div>
				</div>
			</div>
		</article>
	</div>
</main>
<?php $this->get_footer(); ?>