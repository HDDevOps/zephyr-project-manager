<?php
	/**
	* Premium features page
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}
?>

<main id="zpm_welcome_page">
	<div id="zpm_welcome_container" class="zpm_body">
		<h1>Zephyr Project Manager Pro</h1>
		<div id="zpm-welcome-content">
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-asana.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Asana Integration</h3>
					<p class="zpm-feature-description">View your Asana projects and tasks in WordPress and import them into Zephyr to manage all your projects and tasks in one place.</p>
				</div>
			</span>
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-custom.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Custom Fields</h3>
					<p class="zpm-feature-description">Create more detailed and personalized tasks by creating your own custom fields and assiging them to any tasks.</p>
				</div>
			</span>
			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-kanban.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Kanban Boards</h3>
					<p class="zpm-feature-description">View your tasks in a Kanban board style and drag and drop them into different columns to keep them organized.</p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-frontend.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Beautiful Frontend</h3>
					<p class="zpm-feature-description">Includes a beautiful Frontend Project Manager that is customizable and easy to use to allow users or yourself to manage projects from the frontend with the dedicated user interface.</p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-stats.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Reporting and Advanced Search</h3>
					<p class="zpm-feature-description">Create details project progress reports for any project and customize which data should be shown in the report. You can then print or save the reports.</p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-custom.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Task Templates</h3>
					<p class="zpm-feature-description">Create reusable and useful templates for your tasks with your custom fields, to add to your tasks in a single click and customize your projects and tasks to your needs.</p>
				</div>
			</span>

			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-ellipsis.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">Mobile App</h3>
					<p class="zpm-feature-description">Manage your projects and tasks on the go and stay up to date from anywhere. Increase your productivity now with this beautifully designed app and get more work done.</p>
				</div>
			</span>

			<span id="zpm-mobile-feature" class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/zephyr-tasks-framed.png'; ?>">
				</div>
			</span>


			<span class="zpm-col-4">
				<div class="zpm-feature-image-holder">
					<img class="zpm-feature-image" src="<?php echo ZPM_PLUGIN_URL . 'assets/img/icon-ellipsis.png'; ?>">
				</div>
				<div class="zpm-feature-text">
					<h3 class="zpm-feature-title">And much more...</h3>
					<p class="zpm-feature-description">Plus many more features. If you have any feature suggestions I would be more than happy to hear them. You can contact me at dylanjkotze@gmail.com.</p>
				</div>
			</span>
		</div>
		<a class="zpm_button" href="https://zephyr-one.com/purchase-pro" target="_blank">Get Zephyr Pro Now</a>
	</div>
</main>
<?php $this->get_footer(); ?>