<?php 
	/**
	* Template for displaying the task list
	*/

	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;

	$tasks = Tasks::get_tasks();
	if (isset($filters['user_tasks'])) {
		$tasks = Tasks::get_user_tasks( $filters['user_tasks'] );
	}
?>

<div class="zpm_task_list">
	<?php if (!empty($tasks)) : ?>
		<?php foreach ($tasks as $task) : ?>
			<?php
				$project = Projects::get_project($task->project);
				if (isset($_GET['project']) && $_GET['project'] !== $task->project || (isset($_POST['project_id']) && $_POST['project_id'] !== $task->project)) { 
					continue; 
				} 

				if (is_admin()) {
					echo Tasks::new_task_row($task);
				} else {
					?> <a href=?action=task&id=<?php echo $task->id; ?>><?php
					echo Tasks::new_task_row($task);
					?> </a> <?php
				}
				
			?>
		<?php endforeach; ?>
	<?php else: ?>
		<p class="zpm_message_center">There are no tasks yet.</p>
	<?php endif; ?>
</div>