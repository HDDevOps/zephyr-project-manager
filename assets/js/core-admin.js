jQuery(document).ready(function($) {

	// Initialization functions
	cct_initialize();
	
	// Initialize
	function cct_initialize() {
		var task_loading_ajax = null;

		ZephyrProjects.initialize_calendar();

		$('#zpm_project_color').wpColorPicker();
		$('#zpm_category_color').wpColorPicker();
		$('#zpm_colorpicker_primary, #zpm_colorpicker_primary_dark, #zpm_colorpicker_primary_light').wpColorPicker();
		$('#zpm_edit_project_start_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_edit_project_due_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_new_task_start_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_new_task_due_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_edit_task_start_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_edit_task_due_date').datepicker({dateFormat: 'yy-mm-dd' });
		$('#zpm_edit_project_end_date').datepicker({dateFormat: 'yy-mm-dd' });
		$("#zpm_project_due_date").datepicker({dateFormat: 'yy-mm-dd' });
		$("#zpm_task_due_date").datepicker({dateFormat: 'yy-mm-dd' });

		$('body').append('<div id="zpm_notifcation_holder"></div>');
	}

	// Dropdown menu
	$('body').on('click', function(e){
		target = $(e.target);
		if (target.hasClass('disabled')) {
			return;
		}
		if (target.find('.zpm_dropdown_menu').hasClass('active')) {
			target.find('.zpm_dropdown_menu').removeClass('active');
			return;
		}
		if (target.hasClass('active')) {
			return;
		}
		if (!$(e.target).data('zpm-pro-upsell')) {
			$('body').find('.zpm-pro-notice').removeClass('active');
		}
		$('.zpm_dropdown_menu').removeClass('active');
		if ( target.hasClass('zpm_taskbar_link') || target.hasClass('zpm_taskbar_list_item') ) {
			target.closest('.zpm_taskbar_list_item').find('.zpm_dropdown_menu').toggleClass('active');
		} else if ( target.hasClass('zpm_options_button') || target.hasClass('zpm_project_grid_options') || target.hasClass('zpm_project_grid_options_icon') || target.hasClass('zpm_category_option_icon') ) {
			target.find('.zpm_dropdown_menu').toggleClass('active');
		}
	});

	$('.zpm_tab').on('click', function(){
		var target = $(this).data('target');
		$('.zpm_tab').removeClass('active');
		$('.tab-pane').removeClass('active');
		$(this).addClass('active');
		$('.tab-pane[data-section="' + target + '"]').addClass('active');
	});

	$('body').on('click', '[data-zpm-tab-trigger]', function() {
		var tab = $(this).data('zpm-tab-trigger');
		$('body').find('[data-zpm-tab-trigger]').removeClass('zpm_tab_selected');
		$(this).addClass('zpm_tab_selected');
		$('body').find('[data-zpm-tab]').removeClass('zpm_tab_active');
		$('body').find('[data-zpm-tab="' + tab + '"]').addClass('zpm_tab_active');
	});


	/* Upload Profile Picture */
	var mediaUploader;

	$('.zpm_settings_profile_picture').on('click', function() {
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
			text: 'Choose Image'
		}, multiple: false });

	    var image_holder = $('.zpm_settings_profile_image');
	    var image_input = $('#zpm_profile_picture_hidden');

		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			image_holder.css('background-image', 'url(' + attachment.url + ')');
			image_input.val(attachment.url);
		});
		// Open the uploader dialog
		mediaUploader.open();
	});

	// Import tasks via CSV
	var csvFileUploader;
	
	$('#zpm_import_tasks_from_csv').on('click', function() {
		if (csvFileUploader) {
			csvFileUploader.open();
			return;
		}

		csvFileUploader = wp.media.frames.file_frame = wp.media({
			title: 'Select a CSV File',
			button: {
			text: 'Choose File'
		}, multiple: false });
	  
		csvFileUploader.on('select', function() {
			var attachment = csvFileUploader.state().get('selection').first().toJSON();
			zpm_import_tasks(attachment);
		});
		// Open the uploader dialog
		csvFileUploader.open();
	});

	// Import tasks via JSON
	var jsonFileUploader;
	$('#zpm_import_tasks_from_json').on('click', function() {
		if (jsonFileUploader) {
			jsonFileUploader.open();
			return;
		}

		jsonFileUploader = wp.media.frames.file_frame = wp.media({
			title: 'Select a JSON File',
			button: {
			text: 'Choose File'
		}, multiple: false });
	  
		jsonFileUploader.on('select', function() {
			var attachment = jsonFileUploader.state().get('selection').first().toJSON();
			zpm_import_tasks(attachment);
		});
		// Open the uploader dialog
		jsonFileUploader.open();
	});

	// Reset profile picture
	$('#zpm_reset_profile_picture').on('click', function() {
		default_image = $('#zpm_gravatar').val();
		var image_holder = $('.zpm_settings_profile_image');
	    var image_input = $('#zpm_profile_picture_hidden');

	    image_holder.css('background-image', 'url(' + default_image + ')');
		image_input.val(default_image);
	});


	/* Modal */
	// Open modal for user info
	$('.zpm_avatar_image').on('click', function() {
		//ZephyrProjects.open_modal('zpm_user_info_modal');
	});

	/* Charts and Data Vizualization */
	var projects = [];
	var labels = [];

	$.getJSON( zpm_localized.rest_url + 'projects', function( data ) {
		$.each( data, function( key, val ) {
			projects.push({
				id: val.id,
				name: val.name,
				description: val.description,
				date_created: val.date_created,
				date_due: val.date_due,
				completed: val.completed
			});
			labels.push(val.name);
		});


		var canvas = $("#myChart");
		if (canvas.length) {
			var ctx = canvas;
			var chart = new Chart(ctx, {
			    type: 'bar',
			    data: {
			        labels: labels,
			        datasets: [{
			            label: 'Tasks Completed',
			            data: [12, 19, 3, 5],
			            backgroundColor: [
			                'rgba(255, 99, 132, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)'
			            ],
			            borderColor: [
			                'rgba(255,99,132,1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)'
			            ],
			            borderWidth: 1
			        }, {
			            label: 'Tasks Remaining',
			            data: [10, 5, 9, 17],
			            backgroundColor: [
			                'rgba(255, 99, 132, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)'
			            ],
			            borderColor: [
			                'rgba(255,99,132,1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)'
			            ],
			            borderWidth: 1
			        }]
			    },
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        }
			    }
			});
		}
	});
	
	// Display task reminders and notifications
	ZephyrProjects.task_reminders();

	jQuery('body').on('click', '.zpm_floating_notification_button', function(){
		var notification = jQuery(this).closest('.zpm_floating_notification');
		var task_id = notification.data('id');
		var item = notification.data('item');
		notification.addClass('dismissed');
		localStorage.setItem(item + task_id, true);
	});

	$('#zpm_add_new_btn').on('click', function() {
		$(this).toggleClass('active');
	});

	$('#zpm_create_quickproject, #zpm_first_project, #zpm_create_new_project').on('click', function() {
		zpm_close_quicktask_modal();
		ZephyrProjects.open_modal('zpm_project_modal');
	});

	$('.zpm_fancy_item').on('click', function(){
		zpm_close_quicktask_modal();
	});

	function zpm_close_quicktask_modal() {
		$('body').find('#zpm_add_new_btn').removeClass('active');
		$('body').find('#' + $('body').find('#zpm_add_new_btn').data('zpm-dropdown-toggle')).removeClass('active');
	}

	$('body').on('click', '.zpm_close_modal', function() {
		var modalId = $(this).closest('.zpm-modal').attr('id');
		if (modalId == 'zpm_quickview_modal') {
			var backgroundId = 'zpm_quickview_project_background';
			ZephyrProjects.remove_modal(modalId);
		} else {
			ZephyrProjects.close_modal();
		}
	});
	
	$('body').on('click', '[data-zpm-trigger="close_modal"]', function(){
		ZephyrProjects.close_modal();
	});


	// Close modals when clicking on modal background
	$('body').on('click', '.zpm_modal_background', function() {
		var backgroundId = $(this).attr('id');
		
		if (backgroundId == 'zpm_quickview_project_background') {
			var modalId = 'zpm_quickview_modal';
			ZephyrProjects.remove_modal(modalId);
		} else {
			ZephyrProjects.close_modal();
		}
	});

	// New Project Modal
	//var open_modal = $('#zpm_add_new_btn');
	//var close_modal = $('#zpm_project_modal .close');

	var modal = $('.zpm-modal');

	$(window).on('click', function(e){
		if ($(e.target)[0] == modal[0]) {
	        $('body').find('.zpm_modal_background').remove();
    		$('body').find('.zpm-modal').remove();
    	}
	});

	$('body').on('click', '.zpm_project_title', function(e) {
		e.preventDefault();
		if (e.target.className.indexOf('zpm_project_grid_options') > -1) { return; };
		var title = $(this).text();
		var content = '<input type="text" name="zpm_project_title"/>';
		content += '<input type="text" name="zpm_project_description">';
	});

	// Custom Select Fields
	$('body').on('click', '.zpm_select_option', function(e){
		var data = $(this).data();
		var optionValue = $(this).html();

		$(this).closest('.zpm_select_dropdown').find('.zpm_select_option').each(function(){
			$(this).removeClass('selected');
		});

		$(this).closest('.zpm_select_trigger').find('.zpm_selected_option').html(optionValue);
		$(this).closest('.zpm_select_trigger').removeClass('active');
		$(this).closest('.zpm_select_dropdown').removeClass('active');
		$(this).addClass('selected');
	});

	$('body').on('click', '.zpm_select_trigger', function(e){
		var data = $(this).data();
		$('.zpm_select_trigger').each(function(){
			var thisData = $(this).data();
		});

		if (e.target.className.indexOf('zpm_select_option') <= -1) {
			$(this).toggleClass('active');
			$(this).find('.zpm_select_dropdown').toggleClass('active');
		}
	});

	function zpmNewModal(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {
		var modal_navigation = (typeof navigation !== 'undefined' && navigation !== '') ? navigation : '';
		var modal_settings = (typeof options !== 'undefined' && options !== '') ? '<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown"><i class="dashicons dashicons-menu"></i>' + options + '</span>' : '';
		var modal = '<div id="' + modal_id + '" class="zpm-modal" data-modal-action="remove" data-task-id="' + task_id + '" data-project-id="' + project_id + '">' +
					'<div class="zpm_modal_body">' +
						'<h2>' + subject + '</h2>' +
						'<h3 class="zpm_modal_task_name">' + header + '</h3>' + modal_settings +
						modal_navigation +
						'<div class="zpm_modal_content">' + content + '</div>' +
						'<div class="zpm_modal_buttons">' + buttons + '</div>' + 
					'</div>' +
				'</div';
		$('body').append(modal);
		ZephyrProjects.open_modal(modal_id);
	}

	$('body').on('click', '#zpm_quick_task_add', function(){
		$('body').find('.zpm_quicktask_container').toggleClass('active');
	});

	$('.zpm_project_progress_bar').each( function() {
		var total_tasks = $(this).data('total_tasks');
		var completed_tasks = $(this).data('completed_tasks')
		var width = (total_tasks !== 0) ? ((completed_tasks / total_tasks) * 100) : 0;
		$(this).css('width', width + '%');
	});

	$('body').on('click', '.zpm_project_title', function(e){
		e.preventDefault();
		var menu_ids = [
			'zpm_add_project_to_dashboard',
			'zpm_delete_project',
			'zpm_copy_project',
			'zpm_export_project',
			'zpm_export_project_to_csv',
			'zpm_export_project_to_json',
			'zpm_print_project'
		];

		if (e.target.className.indexOf('zpm_project_grid_options') > -1 || $.inArray($(e.target).attr('id'), menu_ids) > -1 ) {
			return;
		};

		var title = $(this).find('.zpm_project_grid_name').text();
		var description = $(this).closest('.zpm_project_grid_cell').find('.zpm_project_description').text();
		var project_link = $(this).attr('href');
		var project_id = $(this).data('project_id');
		var buttons = '<a class="zpm_button" href="' + project_link + '">Go to Project</a>';
		var data = {
			project_id: project_id
		};

		ZephyrProjects.get_tasks(data, function(response){
			$('body').find('.zpm_task_loader').hide();
			$('body').find('#zpm_quickview_modal').attr('data-project-id', data.project_id);
			$('body').find('#zpm_quickview_modal .zpm_modal_content').html(response);
		});

		ZephyrProjects.project_modal('', $.trim(title), description, buttons, 'zpm_quickview_modal');

		ZephyrProjects.get_project(data, function(response){
			$('body').find('#zpm-project-modal-overview').html(response.overview_html);
			$('body').find('#zpm-project-modal-discussion').html(response.comments_html);
			$('body').find('#zpm-project-modal-due-date').html(response.date_due);
		});
	});

	$('body').on('click', '.zpm_task_complete', function(){
		$(this).toggleClass('completed');
	});

	/* Open new task modal on Tasks page */
	$('body').on('click', '#zpm_task_add_new', function(){
		$('body').find('#zpm_create_task').addClass('active');
		ZephyrProjects.open_modal();
		$('body').find('#zpm-new-task-template-select').trigger('change');
		$('body').find('select#zpm_new_task_project').chosen({
		    disable_search_threshold: 10,
		    no_results_text: "Oops, no projects found!",
		    width: "100%"
		});
	});

	/* Open new task modal for the QuickAdd menu */
	$('body').on('click', '#zpm_quickadd_task', function(){
		ZephyrProjects.open_modal('zpm_create_task');
		$('body').find('#zpm-new-task-template-select').trigger('change');
		$('body').find('select#zpm_new_task_project').chosen({
		    disable_search_threshold: 10,
		    no_results_text: "Oops, no projects found!",
		    width: "100%"
		});
	});

	/* Open new task modal when there are no tasks */
	$('body').on('click', '#zpm_first_task', function(){
		$('body').find('#zpm_task_add_new').trigger('click');
	});

	/* Open new task modal on Project editor page */
	$('body').on('click', '#zpm_add_new_project_task', function() {
		ZephyrProjects.open_modal('zpm_create_task');
		$('body').find('#zpm-new-task-template-select').trigger('change');
	});


	/* Create a new task */
	$('#zpm_save_task').on('click', function() {
		// Close modal, save task and update task list
		var name = $('#zpm_new_task_name').val();
		var description = $('#zpm_new_task_description').val();
		var project = $('#zpm_new_task_project').val();
		var assignee = $('#zpm_new_task_assignee').val();
		var due_date = $('#zpm_new_task_due_date').val();
		var start_date = $('#zpm_new_task_start_date').val();
		var team = $('#zpm-new-task-team-selection').val();

		var custom_fields = [];
		$('body').find('.zpm_task_custom_field').each(function(){
			var id = $(this).data('zpm-cf-id');
			var value = $(this).val();
			custom_fields.push({
				id: id,
				value: value
			});
		});

		if (name == '') { 
			alert('Please enter a name for the task.');
			return; 
		}

		var subtasks = [];

		$('.zpm_task_subtask_item').each(function(){
			if ($(this).val() !== '') {
				subtasks.push($(this).val());
			}
		})

		var data = {
        	task_name: name,
			task_description: description,
			subtasks: subtasks,
			task_project: project,
			task_assignee: assignee,
			task_due_date: due_date,
			task_start_date: start_date,
			task_custom_fields: custom_fields,
			team: team
        }

        if ($('body').find('#zpm-new-task-kanban-id').length > 0 && $('body').find('#zpm-new-task-kanban-id').val() !== '') {
        	data.kanban_col = $('#zpm-new-task-kanban-id').val();
        }

		ZephyrProjects.create_task(data, function(response){
        	var new_task = 	response.new_task_html;
			$('body').find('.zpm_task_list').prepend(new_task);
			$('body').find('.zpm_message_center').remove();
			$('body').find('#zpm_task_option_container').removeClass('zpm_hidden');
			$('body').find('#zpm_task_list_container').removeClass('zpm_hidden');
			$('body').find('.zpm_no_results_message').addClass('zpm_hidden');
			$('.zpm_no_results_message').hide();

			if ($('body').find('#zpm_kanban_container_1').length !== 0) {
				var container = $('body').find('.zpm-delete-kanban-row[data-kanban-id="' + response.kanban_col + '"]').closest('.zpm_kanban_row').find('.zpm_kanban_container');
				container.append(response.kanban_html);
				container.animate({ scrollTop: container.prop("scrollHeight")}, 1000);
			}
        });

		ZephyrProjects.close_modal();

		$('body').find('#zpm_create_task #zpm_new_task_name').val('');
		$('body').find('#zpm_create_task #zpm_new_task_description').val('');
		$('body').find('#zpm_create_task #zpm_new_task_due_date').val('');
		if ($('body').find('#zpm-project-id').length <= 0) {
			$('body').find('#zpm_create_task #zpm_new_task_project').val('-1');
		}
		$('body').find('#zpm_create_task #zpm_new_task_assignee').val('-1');
		$('body').find('#zpm_create_task #zpm_task_subtasks').html('');
	});

	$('#zpm_save_changes_task').on('click', function() {
		var taskId = $(this).data('task-id');
		var name = $('#zpm_edit_task_name').val();
		var description = $('#zpm_edit_task_description').val();
		var assignee = $('#zpm_edit_task_assignee').val();
		var due_date = $('#zpm_edit_task_due_date').val();
		var start_date = $('#zpm_edit_task_start_date').val();
		let project_id = $('body').find('#zpm_edit_task_project').val();
		var team = $('body').find('#zpm-edit-task-team-selection').val();
		var subtasks = [];
		var custom_fields = [];

		$('body').find('#zpm_task_edit_custom_fields .zpm_task_custom_field').each(function(){
			var id = $(this).data('zpm-cf-id');
			var value = $(this).val();
			custom_fields.push({
				id: id,
				value: value
			});
		});

		$(this).html('Saving...');

		ZephyrProjects.update_task({
			task_id: taskId,
			task_name: name,
			task_description: description,
			task_assignee: assignee,
			task_subtasks: subtasks,
			task_due_date: due_date,
			task_start_date: start_date,
			task_custom_fields: custom_fields,
			task_project: project_id,
			team: team
		}, function(response){
			$('#zpm_save_changes_task').html('Save Changes');
		});
	});

	/* Update task completion status */
	$('body').on('click', '.zpm_task_mark_complete', function() {
		var task_id = $(this).data('task-id');

		if ($(this).attr('checked')) {
			var data = {
				id: task_id,
				completed: 1
			}

			ZephyrProjects.complete_task(data, function(response){});
			$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').addClass('zpm_task_complete');
			$('body').find('.zpm_task_mark_complete[data-task-id="' + task_id + '"]').attr('checked', 'checked');
		} else {
			$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').removeClass('zpm_task_complete');
			$('body').find('.zpm_task_mark_complete[data-task-id="' + task_id + '"]').removeAttr('checked');
			var data = {
				id: task_id,
				completed: 0
			}

			ZephyrProjects.complete_task(data, function(response){});
		}
		
		
	});

	/* Mark a task as complete */
	$('body').on('click', '.zpm_task_mark_complete', function(){
		var checked = $(this).attr('checked');
		var task_id = $(this).closest('li').data('task-id');

		if (checked == 'checked') {
			$(this).closest('li').addClass('zpm_task_completed');
		} else {
			$(this).closest('li').removeClass('zpm_task_completed');
		}		
	});

	/* Delete a task from the list */
	$('body').on('click', '.zpm_delete_task', function() {
		var task_id = $(this).closest('li').data('task-id');
		$(this).closest('li').hide();
	});

	// Select project type 
	$('body').on('click', '.zpm_modal_item', function() {
		var type = $(this).find('.image').data('project-type');
		$('#zpm-project-type').val(type);
		$('body').find('.zpm_modal_item .image').removeClass('zpm_project_selected');
		$(this).find('.image').addClass('zpm_project_selected');
	});

	// Add new project via modal
	$('body').on('click', '#zpm_modal_add_project', function() {
		var name = $(this).closest('#zpm_project_modal').find('.zpm_project_name_input').val();
		var project_type = $(this).closest('#zpm_project_modal').find('.zpm_project_selected').data('project-type');
		var type = $('body').find('#zpm-project-type').val();

		if (name == '') {
			$(this).closest('#zpm_project_modal').find('.zpm_project_name_input').after('<span class="zpm_validation_error">Please enter a project name.</span>');
		} else {
			ZephyrProjects.close_modal();
			ZephyrProjects.create_project({
				project_name: name,
				project_description: '',
				project_team: '1',
				project_categories: '',
				project_due_date: '2018-14-03',
				type: type
			}, function(response){
				$('body').find('#zpm_project_manager_display').removeClass('zpm_hide');
				$('body').find('.zpm_no_results_message').hide();
				$('body').find('.zpm_project_grid').prepend(response.html);
			});
		}
	});

	var task_loading_ajax = null;

	$('body').on('click', '.zpm_task_list_row', function(e) {
		if (e.target.className.indexOf('zpm_task_mark_complete') > -1) {
			return;
		}
		var data = $(this).data();
		var task_name = data.taskName;
		var task_id = data.taskId;
		var task_view_modal = $('body').find('#zpm_task_view_container');
		task_view_modal.html('<div class="zpm_task_loader"></div>');
		var data = {
			task_id: task_id
		}

		if ($(this).closest('#zpm_quickview_modal').length > 0) {
			var url = zpm_localized.tasks_url + '&action=view_task&task_id=' + task_id;
			var win = window.open(url, '_blank');
  			win.focus();
		} else {
			ZephyrProjects.view_task(data, function(response){
				task_view_modal.html(response);
			});

			ZephyrProjects.open_modal('zpm_task_view_container');
			$('body').find('#zpm_task_view_container').attr('data-task-id', task_id);
		}
	});

	$('body').on('click', '#zpm_create_quicktask', function() {
		var quickTaskDataHolder = $(this).closest('.zpm_quicktask_container');
		var taskName = quickTaskDataHolder.find('#zpm_quicktask_name');
		var taskDescription = quickTaskDataHolder.find('#zpm_quicktask_description');
		var taskDueDate = quickTaskDataHolder.find('#zpm_quicktask_date');
		var taskProject = $(this).closest('.zpm-modal').data('project-id');
		var taskAssignee = quickTaskDataHolder.find('#zpm_quicktask_select_assignee');
		quickTaskDataHolder.removeClass('active');

		$('body').find('#zpm_quick_task_add').html('Saving task...').addClass('saving');

		var data = {
			task_project: taskProject,
			task_name: taskName.val(),
			task_description: taskDescription.val(),
			task_assignee: taskAssignee.val(),
			task_due_date: taskDueDate.val()
		}

		ZephyrProjects.create_task(data, function(response){
			$('body').find('.zpm_task_list').prepend(response.new_task_html);
			$('body').find('#zpm_quick_task_add').html('Add Task').removeClass('saving');
			taskName.val(''); taskDescription.val(''); taskDueDate.val(''); taskAssignee.val('');
		});
	});


	// Execute file actions
	$('body').on('click', '.zpm_file_action, .zpm_file_preview', function(){
		var action = $(this).data('zpm-action');
		var target = $(this).closest('.zpm_file_item');

		if (action == 'download_file') {
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = target.data('attachment-name');
			link.href = target.data('attachment-url');
			link.click();
		}
		if (action == 'show_info') {
			var file_data = target.data();
			var html = '<div><label class="zpm_label">File Link</label><p><a class="wppm_link" href="' + file_data.attachmentUrl + '">' + file_data.attachmentUrl + '</a></p><label class="zpm_label">Task</label><p>' + file_data.taskName + '</p><label class="zpm_label">Date Uploaded</label><p>' + file_data.attachmentDate + '</p></div>';
			zpmNewModal('File Info', file_data.attachmentName, html, '', 'zpm_file_info_modal');
		}
		if (action == 'remove_file') {
			if (confirm('Are you sure you want to delete this file?')) {
				ZephyrProjects.remove_comment({
					comment_id: target.data('attachment-id'),
				}, function(){
					ZephyrProjects.notification('File removed.');
				});
				$(this).closest('.zpm_file_item_container').remove();
			}
		}
	});

	// Like a task
	$('body').on('click', '#zpm_like_task_btn', function(e) {
		$(this).toggleClass('zpm_liked');
		var task_id = $(this).data('task-id');
		var data = {
			task_id: task_id
		}

		ZephyrProjects.like_task(data);
	});

	// Follow a task
	$('body').on('click', '#zpm_follow_task', function(){
		var task_id = $(this).closest('#zpm_task_view_container').data('task-id');
		var data = {
			task_id: task_id
		}

		$('body').find('#zpm_follow_task').toggleClass('zpm_following').removeClass('lnr-plus-circle').addClass('lnr-redo').addClass('zpm_spin');
		
		ZephyrProjects.follow_task(data, function(response){
			$('body').find('#zpm_follow_task').removeClass('lnr-redo').removeClass('zpm_spin').addClass('lnr-plus-circle');
			if (response.following) {
				$('body').find('.zpm_task_follower[data-user-id="' + response.user_id + '"]').remove();
			} else {
				$('body').find('#zpm_task_following').append(response.html);
			}
		});
	});

	// Custom Dropdown
	$('body').find('#zpm_new_task_assignee').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no users found.",
	    width: "50%"
	});

	$('body').find('#zpm_edit_task_assignee').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no users found.",
	    width: "50%"
	});

	$('body').find('#zpm_edit_task_assignee').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no users found.",
	    width: "100%"
	});

	$('body').find('#zpm-edit-task-team-selection').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no teams found.",
	    width: "100%"
	});

	$('body').find('#zpm_edit_task_project').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no projects found.",
	    width: "100%"
	});

	// Edit task team changed
	$('body').on('change', '#zpm-edit-task-team-selection', function(){
		let teamId = $(this).val();

		updateEditTaskTeamMembers(teamId);
	});

	var zpmTeamValue = $('#zpm-edit-task-team-selection').val();

	if (typeof zpmTeamValue !== 'undefined' && zpmTeamValue !== -1) {
		//updateEditTaskTeamMembers(zpmTeamValue);
		ZephyrProjects.getTeam({ 
			id: zpmTeamValue
		}, function(res) {
			
			if (res != null) {
				$('#zpm_edit_task_assignee > option').hide();
				$('#zpm_edit_task_assignee > option[value="-1"]').show();
				$.each(res.members, function(key, val) {
					let userId = val.id;
					$('#zpm_edit_task_assignee > option[value="' + userId + '"]').show();
	        		$('#zpm_edit_task_assignee').trigger("chosen:updated");
				});
			}
		});
	}

	function updateEditTaskTeamMembers(teamId) {
		$('#zpm_edit_task_assignee > option').show();
        $('#zpm_edit_task_assignee').trigger("chosen:updated");

		ZephyrProjects.getTeam({ 
			id: teamId
		}, function(res) {
			$('#zpm_edit_task_assignee').val('-1');
			$('#zpm_edit_task_assignee').trigger("chosen:updated");
			if (res != null) {
				$('#zpm_edit_task_assignee > option').hide();
				$('#zpm_edit_task_assignee > option[value="-1"]').show();
				$.each(res.members, function(key, val) {
					let userId = val.id;
					$('#zpm_edit_task_assignee > option[value="' + userId + '"]').show();
	        		$('#zpm_edit_task_assignee').trigger("chosen:updated");
				});
			}
		});
	}

    // Task Subtasks
    $('body').on('click', '#zpm_task_add_subtask', function(){
    	var newSubTask = '<span class="zpm_task_subtask"><input type="text" class="zpm_task_subtask_item" placeholder="New Subtask" value=""/><i class="zpm_delete_subtask_icon dashicons dashicons-no-alt"></i></span>';
    	$('body').find('#zpm_task_subtasks').append(newSubTask);
    	var lastSubTask = $('body').find('.zpm_task_subtask:last-of-type');
    	var scrollParent = $('body').find('.zpm_modal_content');
    	
	    scrollParent.animate({
	        scrollTop: lastSubTask.offset().top
	    }, 0);
	});

	$('body').on('click', '.zpm_delete_subtask_icon', function(){
    	$(this).closest('.zpm_task_subtask').remove();
	}); 

	// Task Options
	$('body').on('click', '.zpm_modal_options_btn', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$(this).find('.zpm_modal_dropdown').toggleClass('active');
	}); 

	// Copy a task
	$('body').on('click', '#zpm_copy_task', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var buttons = '<button id="zpm_copy_task_btn" class="zpm_button">Create New Task</button>';
		
		var optionsList = [
			{
				name: 'Task Description',
				default: 'checked',
				value: 'description'
			},{
				name: 'Assignee',
				default: 'checked',
				value: 'assignee'
			},{
				name: 'Subtasks',
				default: 'checked',
				value: 'subtasks'
			},{
				name: 'Attachments',
				default: '',
				value: 'attachments'
			},{
				name: 'Start Date',
				default: 'checked',
				value: 'start_date'
			},{
				name: 'Due Date',
				default: 'checked',
				value: 'due_date',
			}
		];

		var options = '<ul class="zpm_copy_task_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = options + 
				'<li>' + 
					'<label for="zpm_copy_task_option_' + i + '" class="zpm_checkbox_label">' +
						'<input type="checkbox" id="zpm_copy_task_option_' + i + '" name="zpm_copy_task_option_' + i + '" class="zpm_copy_task_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
							'<div class="zpm_main_checkbox">'+
								'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
								'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
								'<polyline points="4 11 8 15 16 6"></polyline>' +
							'</svg>' +
						'</div>' +
				    '</label>' +
					optionsList[i].name +  
				'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_copy_task_body"><h5 class="zpm_copy_project_title">Include: </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal('Copy Task', '<input id="zpm_copy_task_name" value="Copy of ' + $.trim(taskName) + '" placeholder="Task Name" />', content, buttons, 'zpm_copy_task_modal', taskId);
    	ZephyrProjects.open_modal('zpm_copy_task_modal');
	}); 

	$('body').on('click', '#zpm_copy_task_btn', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var newName = $('body').find('#zpm_copy_task_name').val();
		var copySettings = [];

		$('.zpm_copy_task_options .zpm_copy_task_option').each(function(){
			var checked = ($(this).attr('checked') == 'checked') ? true : false;
			var optionValue = $(this).data('option-value');

			if (checked) {
				copySettings.push(optionValue);
			}
		});

		var data = {
			task_id: taskId,
			task_name: newName,
			copy_options: copySettings
		}
		ZephyrProjects.copy_task(data, function(response){
			$('body').find('.zpm_task_list').prepend(response.html);
			$('body').find('.zpm_message_center').remove();
		});

		ZephyrProjects.close_modal();
	});

	// Copy a project
	$('body').on('click', '#zpm_copy_project', function(){
		var projectId = $(this).closest('.zpm_project_item').data('project-id');
		var projectName = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		var buttons = '<button id="zpm_copy_project_btn" class="zpm_button">Create New Project</button>';
		
		var optionsList = [
			{
				name: 'Description',
				default: 'checked',
				value: 'description'
			},{
				name: 'Tasks',
				default: 'checked',
				value: 'tasks'
			},{
				name: 'Attachments',
				default: 'checked',
				value: 'attachments'
			},{
				name: 'Start Date',
				default: 'checked',
				value: 'start_date'
			},{
				name: 'Due Date',
				default: 'checked',
				value: 'due_date',
			}
		];

		var options = '<ul class="zpm_copy_project_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = 	options + 
						'<li>' + 
							'<label for="zpm_copy_project_option_' + i + '" class="zpm_checkbox_label">' +
								'<input type="checkbox" id="zpm_copy_project_option_' + i + '" name="zpm_copy_project_option_' + i + '" class="zpm_copy_project_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
									'<div class="zpm_main_checkbox">'+
										'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
										'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
										'<polyline points="4 11 8 15 16 6"></polyline>' +
									'</svg>' +
								'</div>' +
						    '</label>' +
							optionsList[i].name +  
						'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_copy_project_body"><h5>Include: </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal('Copy Project', '<input id="zpm_copy_project_name" value="Copy of ' + $.trim(projectName) + '" placeholder="Project Name" />', content, buttons, 'zpm_copy_project_modal', projectId, '', projectId);
    	ZephyrProjects.open_modal('zpm_copy_project_modal');
	}); 

	$('body').on('click', '#zpm_copy_project_btn', function(){
		var project_id = $(this).closest('.zpm-modal').data('project-id');
		var new_name = $('body').find('#zpm_copy_project_name').val();
		var copy_options = [];

		$('.zpm_copy_project_options .zpm_copy_project_option').each(function(){
			var checked = ($(this).attr('checked') == 'checked') ? true : false;
			var option_value = $(this).data('option-value');
			if (checked) {
				copy_options.push(option_value);
			}
		});

		var data = {
			project_id: project_id,
			project_name: new_name,
			copy_options: copy_options
		}

		ZephyrProjects.copy_project(data, function(response){
			$('body').find('.zpm_project_grid').prepend(response.html);
		});
		ZephyrProjects.close_modal();
	});
	

	// Convert task to Project
	$('body').on('click', '#zpm_convert_task', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var buttons = '<button id="zpm_convert_task_btn" class="zpm_button">Convert Task</button>';
		
		var optionsList = [
			{
				name: 'Task Description as Project Description',
				default: 'checked',
				value: 'description'
			},{
				name: 'Subtasks as Project Tasks',
				default: 'checked',
				value: 'subtasks'
			},{
				name: 'Assignee as Project creator',
				default: 'checked',
				value: 'assignee'
			}
		];

		var options = '<ul class="zpm_convert_task_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = 	options + 
						'<li>' + 
							'<label for="zpm_convert_task_option_' + i + '" class="zpm_checkbox_label">' +
								'<input type="checkbox" id="zpm_convert_task_option_' + i + '" name="zpm_convert_task_option_' + i + '" class="zpm_convert_task_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
									'<div class="zpm_main_checkbox">'+
										'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
										'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
										'<polyline points="4 11 8 15 16 6"></polyline>' +
									'</svg>' +
								'</div>' +
						    '</label>' +
							optionsList[i].name +  
						'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_convert_task_body"><h5>Include: </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal('Convert Task to Project', '<input id="zpm_convert_task_name" value="Project: ' + $.trim(taskName) + '" placeholder="Project Name" />', content, buttons, 'zpm_convert_task_modal', taskId);
    	ZephyrProjects.open_modal('zpm_convert_task_modal');
	}); 

	$('body').on('click', '#zpm_convert_task_btn', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var newName = $('body').find('#zpm_convert_task_name').val();

		var convertSettings = [];
		$('.zpm_convert_task_options .zpm_convert_task_option').each(function(){
			var checked = ($(this).attr('checked') == 'checked') ? true : false;
			var optionValue = $(this).data('option-value');
			if (checked) {
				convertSettings.push(optionValue);
			}
		});

		var data = {
			task_id: taskId,
			project_name: newName,
			convert_options: convertSettings
		}

		ZephyrProjects.task_to_project(data, function(response){

		});

		ZephyrProjects.close_modal();
	});


	// Export a task to JSON
	$('body').on('click', '#zpm_export_task_to_json', function() {
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();

		var data = {
			task_id: taskId,
			export_to: 'json'
		}

		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});
	});

	// Export a task to CSV
	$('body').on('click', '#zpm_export_task_to_csv', function() {
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var data = {
			task_id: taskId,
			export_to: 'csv'
		}

		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});

	});

	// Export all tasks to JSON
	$('body').on('click', '#zpm_export_all_tasks_to_json', function() {

		var data = {
			export_to: 'json'
		}

		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'All Tasks.json';
			link.href = response;
			link.click();
		});
	});

	// Export all tasks to CSV
	$('body').on('click', '#zpm_export_all_tasks_to_csv', function() {

		var data = {
			export_to: 'csv'
		}
		
		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'All Tasks.json';
			link.href = response;
			link.click();
		});
	});

	// Print a task
	$('body').on('click', '#zpm_print_task', function(){
		setTimeout(function(){
			var printContents = $('body').find('#zpm_task_view_container').html();
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
		}, 500);
	});

	$('body').on('click', '.zpm_custom_dropdown', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$(this).toggleClass('active');
    	$(this).find('#' + dropdown).toggleClass('active');
	}); 

	// Filter Tasks
	$('body').on('click', '#zpm_filter_tasks .zpm_selection_option, #zpm-tasks-filter-nav .zpm_selection_option', function() {
		var filter = $(this).data('zpm-filter');
		var option = $(this).html();
		var user_id = '-1';
		if (filter == '0') {
			user_id = $(this).data('user-id');
		}

		$(this).closest('.zpm_custom_dropdown').find('.zpm_selected_option').html(option);
		zpm_loader_modal('Loading tasks...');

		var data = {
			zpm_filter: filter,
			zpm_user_id: user_id
		}

		ZephyrProjects.filter_tasks(data, function(response){
			$('body').find('.zpm_task_list').html(response.html);
			zpm_close_loader_modal();
		});
	});

	$('body').on('click', '#zpm_filter_tasks_icon', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$('#zpm_filter_tasks').toggleClass('active');
    	$('body').find('#' + dropdown).toggleClass('active');
	}); 

	// Import tasks via CSV or JSON
	function zpm_import_tasks(attachment) {
		if (attachment.mime == 'text/csv') {
			ZephyrProjects.close_modal();
			zpmNewModal('Import Tasks', 'Importing via CSV', '<div id="zpm_csv_task_import_data"></div>', '<button data-zpm-trigger="close_modal" class="zpm_button zpm_button_borderless" id="zpm_import_csv_data_btn">Close</button>', 'zpm_import_csv_data_modal');
	    	ZephyrProjects.open_modal('zpm_import_csv_data_modal');

			var data = {
				zpm_file: attachment.url,
				zpm_import_via: 'csv'
			}

			ZephyrProjects.upload_tasks(data, function(response){
				var length = (response.tasks.length - 1);
				var output = '<h5>Imported ' + length + ' tasks:</h5><ul id="zpm_csv_task_list">';
				for (var i = 1; i < response.tasks.length; i++) {
					var uploaded = (response.tasks[i].already_uploaded) ? 'zpm_task_exists' : '';
					var task_exists = (response.tasks[i].already_uploaded) ? 'Task already exists: ' : '';
					output = output + '<li  class="zpm_imported_task ' + uploaded + '">' + task_exists + response.tasks[i].name + ' ' + response.tasks[i].description + ' (' + response.tasks[i].project + ')</li>';
				}
				output = output + '</ul>';
				$('body').find('#zpm_csv_task_import_data').html(output);
				$('.zpm_task_list').prepend(response.html);
			});

		} else if (attachment.mime == 'application/json') {
			ZephyrProjects.close_modal();
			zpmNewModal('Import Tasks', 'Importing via JSON', '<div id="zpm_json_task_import_data"></div>', '<button data-zpm-trigger="close_modal" class="zpm_button zpm_button_borderless" id="zpm_import_json_data_btn">Close</button>', 'zpm_import_json_data_modal');
	    	ZephyrProjects.open_modal('zpm_import_json_data_modal');

	    	var data = {
				zpm_file: attachment.url,
				zpm_import_via: 'json'
			}

			ZephyrProjects.upload_tasks(data, function(response){
				var length = (response.tasks.length - 1);
				var output = '<h5>Imported ' + length + ' tasks:</h5><ul id="zpm_json_task_list">';
				for (var i = 0; i < response.tasks.length; i++) {
					var uploaded = (response.tasks[i].already_uploaded) ? 'zpm_task_exists' : '';
					var task_exists = (response.tasks[i].already_uploaded) ? 'Task already exists: ' : '';
					output = output + '<li class="zpm_imported_task ' + uploaded + '">' + task_exists + response.tasks[i].name + ' ' + response.tasks[i].description + ' (' + response.tasks[i].project + ')</li>';
				}
				output = output + '</ul>';
				$('body').find('#zpm_json_task_import_data').html(output);
				$('.zpm_task_list').prepend(response.html);
			});
		} else {
			alert('It appears that you have not uploaded a CSV file or a JSON file. Please make sure that the file format is correct and try again.');
		}
	}

	// Hide admin sidebar
	$('body').on('click', '#zpm_hide_wp_adminbar', function(){
		$(document).find('body.wp-admin').toggleClass('folded');
		$(this).toggleClass('folded')
	});

	// Save project settings
	$('body').on('click', '#zpm_project_save_settings', function(){
		var id = $(this).closest('#zpm_project_editor').data('project-id');
		var name = $('body').find('#zpm_edit_project_name').val();
		var description = $('body').find('#zpm_edit_project_description').val();
		var due_date = $('body').find('#zpm_edit_project_due_date').val();
		var start_date = $('body').find('#zpm_edit_project_start_date').val();
		var categories = [];

		$(this).html('Saving...');

		$('.zpm_project_edit_categories').each(function(key, val){
			var checked = $(this).is(':checked');
			var category_id = $(this).data('category-id');
			if (checked) {
				categories.push(category_id);
			}
		});

		var data = {
			project_id: id,
			project_name: name,
			project_description: description,
			project_due_date: due_date,
			project_start_date: start_date,
			project_categories: categories
		}

		ZephyrProjects.update_project(data, function(response){
			$('#zpm_project_name_title').html(name);
			$('#zpm_project_save_settings').html('Save Changes');
		});
	});

	// Delete project
	$('body').on('click', '#zpm_delete_project', function(){
		if (confirm('Are you sure you want to permanently delete this project and all its tasks?')) {
			$(this).closest('.zpm_project_grid_cell').remove();
			var project_id = $(this).closest('.zpm_project_item').data('project-id');
			var project_name = $(this).closest('.zpm_project_title').find('.zpm_project_grid_name').text();

			ZephyrProjects.delete_project({
				project_id: project_id,
				project_name: project_name
			}, function(response){
				if (response.project_count == 0) {
					$('body').find('#zpm_project_manager_display').addClass('zpm_hide');
					$('body').find('#zpm_projects_holder').append('<div class="zpm_no_results_message">No projects created yet. To create a project, click on the \'Add\' button at the top right of the screen or click <a id="zpm_first_project" class="zpm_button_link">here</a></div>');
				}
			});
		}
	});

	// Like a project
	$('body').on('click', '#zpm_like_project_btn', function(e) {
		$(this).toggleClass('zpm_liked');
		var project_id = $(this).data('project-id');
		var data = {
			project_id: project_id
		}

		ZephyrProjects.like_project(data, function(response){

		});
	});

	// Export Project to CSV
	$('body').on('click', '#zpm_export_project_to_csv', function() {
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var project_name = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		var data = {
			project_id: project_id,
			project_name: project_name,
			export_to: 'csv'
		}

		ZephyrProjects.export_project(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'Project - ' + project_name + '.csv';
			link.href = response.project_csv;
			link.click();

			var link_tasks = document.createElement('a');
			document.body.appendChild(link_tasks);
			link_tasks.download = project_name + ' - Tasks.csv';
			link_tasks.href = response.project_tasks_csv;
			link_tasks.click();
		});
	});

	// Export Project to JSON
	$('body').on('click', '#zpm_export_project_to_json', function() {
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var project_name = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		var data = {
			project_id: project_id,
			project_name: project_name,
			export_to: 'json'
		}

		ZephyrProjects.export_project(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});

	});

	// Print a Project
	$('body').on('click', '#zpm_print_project', function(){
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var data = {
			project_id: project_id
		}

		ZephyrProjects.print_project(data, function(response){

		});	
	});

	// Custom Fancy Modal
	$('body').on('click', '[data-zpm-dropdown-toggle]', function(){
		var target = $(this).data('zpm-dropdown-toggle');
		$('body').find('#' + target).toggleClass('active');
	});

	/* Comments and Conversations */
	// Send task comment
	$('body').on('click', '#zpm_task_chat_comment', function() {
		var task_id = $(this).data('task-id');
		var message = $.trim($('body').find('#zpm_chat_message').html());
		message = message.replace(/\<div><br><\/div>/g,'');
		send_message('task', task_id, message);
	});

	// Send project comment
	$('body').on('click', '#zpm_project_chat_comment', function() {
		var task_id = $(this).data('project-id');
		var message = $.trim($('body').find('#zpm_chat_message').html());
		message = message.replace(/\<div><br><\/div>/g,'');
		send_message('project', task_id, message);
	});

	function send_message( subject, subject_id, message ) {
		var attachments = [];
		$('body').find('.zpm_comment_attachment').each(function(){
			var attachment_id = $(this).data('attachment-id');
			attachments.push({
				attachment_id: attachment_id
			});
		});

		if ($(this).text() == 'Comment') {
			$(this).html('Sending...');
		}
		
		$(this).addClass('zpm_message_sending');

		var data = {
			user_id: zpm_localized.user_id,
			subject: subject,
			subject_id: subject_id,
			message: message,
			type: 'message',
			attachments: attachments
		};

		ZephyrProjects.send_comment(data, function(response){
			if ($('#zpm_task_chat_comment').text() == 'Sending...') {
				$('#zpm_task_chat_comment').html('Comment');
			}
			$('#zpm_task_chat_comment').removeClass('zpm_message_sending');
			$('body').find('.zpm_task_comments').prepend(response.html);
			$('body').find('#zpm_chat_message').html('');
			$('#zpm_chat_attachments').html('');
		});
	}

	/* Upload Task File */
	var zpm_file_uploader;

	$('body').on('click', '#zpm_task_chat_files, #zpm_project_chat_files', function() {
		if (zpm_file_uploader) {
			zpm_file_uploader.open();
			return;
		}

		zpm_file_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Files',
			button: {
			text: 'Upload Files'
		}, multiple: false });

		zpm_file_uploader.on('select', function() {
			var attachment = zpm_file_uploader.state().get('selection').first().toJSON();
			var attachment_holder = $('body').find('#zpm_chat_attachments');
			attachment_holder.append('<span data-attachment-id="' + attachment.id + '" class="zpm_comment_attachment">' + attachment.url + '<span class="zpm_remove_attachment lnr lnr-cross"></span></span>');
		});

		// Open the uploader dialog
		zpm_file_uploader.open();
	});

	/* Upload a general file from the Files page */
	var file_uploader;
	$('#zpm_upload_file_btn').on('click', function(){
		if (file_uploader) {
			file_uploader.open();
			return;
		}

		file_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Files',
			button: {
			text: 'Upload Files'
		}, multiple: false });

		file_uploader.on('select', function() {
			var attachment = file_uploader.state().get('selection').first().toJSON();
			var attachment_holder = $('body').find('#zpm_chat_attachments');
			upload_attachment(attachment.id);
			$(this).closest('.zpm_modal_footer').find('#zpm_submit_file').removeClass('inactive');
		});

		// Open the uploader dialog
		file_uploader.open();
	});

	/* Remove a selected attachment */
	$('body').on('click', '.zpm_remove_attachment', function(){
		$(this).closest('.zpm_comment_attachment').remove();
	});

	/* Delete a comment */
	$('body').on('click', '.zpm_delete_comment', function(){
		var comment = $(this).closest('.zpm_comment');
		var comment_id = comment.data('zpm-comment-id');
		comment.remove();

		var data = {
			comment_id: comment_id
		}

		ZephyrProjects.remove_comment(data, function(){
			ZephyrProjects.notification('Message removed.');
		});
	});

	function upload_attachment( attachment_id, attachment_type, subject_id ) {
		ZephyrProjects.notification( 'Uploading file...' );
		var attachment_type = (typeof attachment_type !== 'undefined') ? attachment_type : '';
		var subject_id = (typeof subject_id !== 'undefined') ? subject_id : '';
		var attachments = [{
			attachment_id: attachment_id,
			attachment_type: attachment_type,
			subject_id: subject_id
		}];
		var data = {
			attachments: attachments
		}

		ZephyrProjects.send_comment(data, function(response){
			$('body').find('.zpm_files_container').prepend(response.html);
			ZephyrProjects.notification( 'File uploaded.' );
		});
	}

	/* Open the new subtask modal */
	$('body').on('click', '#zpm_add_new_subtask', function() {
		ZephyrProjects.open_modal('zpm_new_subtask_modal');
	});

	$('body').on('click', '#zpm_save_new_subtask', function() {
		var task_id = $('body').find('#zpm_js_task_id').val();
		var subtask_name = $('body').find('#zpm_new_subtask_name').val();
		var data = {
			task_id: task_id,
			subtask_action: 'new_subtask',
			subtask_name: subtask_name
		}
		$('body').find('#zpm_new_subtask_name').val('');
		ZephyrProjects.close_modal();
		ZephyrProjects.notification('Creating subtask...');

		ZephyrProjects.update_subtasks(data, function(response){
			ZephyrProjects.notification('Subtask saved');
			var new_subtask = '<li class="zpm_subtask_item" data-zpm-subtask="' + response.id + '">' +
								'<input type="checkbox" class="zpm_subtask_is_done" data-task-id="' + response.id + '"/>' +
								'<span class="zpm_subtask_name">' + response.name + '</span>' +
								'<span data-zpm-subtask-id="' + response.id + '" class="zpm_update_subtask">Save Changes</span>' +
								'<span data-zpm-subtask-id="' + response.id + '" class="zpm_delete_subtask">Delete</span>' +
							'</li>';
			var subtask_list = $('body').find('#zpm_subtask_list');
			subtask_list.append(new_subtask);
		});
	});

	/* Delete a subtask from the database */
	$('body').on('click', '.zpm_delete_subtask', function() {
		$(this).closest('.zpm_subtask_item').remove();
		var task_id = $('body').find('#zpm_js_task_id').val();
		var subtask_id = $(this).data('zpm-subtask-id');
		var data = {
			subtask_action: 'delete_subtask',
			subtask_id: subtask_id
		}

		ZephyrProjects.update_subtasks(data, function(response){
			ZephyrProjects.notification('Subtask deleted');
		});
	});

	/* Edit a subtask name */
	$('body').on('click', '.zpm_subtask_name', function() {
		var subtask_name = $(this).text();
		var subtask_parent = $(this).closest('.zpm_subtask_item');
		if (typeof $(this).attr('contentEditable') == 'undefined') {
			$(this).attr('contentEditable', true);
			subtask_parent.find('.zpm_update_subtask').addClass('is_active');
		}
	});

	/* Update subtask name in database */
	$('body').on('click', '.zpm_update_subtask', function() {
		var task_id = $('body').find('#zpm_js_task_id').val();
		var subtask_id = $(this).data('zpm-subtask-id');
		var subtask_parent = $(this).closest('.zpm_subtask_item');
		var subtask = $(this).closest('.zpm_subtask_item').data('zpm-subtask');
		var new_subtask = $(this).closest('.zpm_subtask_item').find('.zpm_subtask_name').html();
		var data = {
			task_id: task_id,
			subtask_id: subtask_id,
			subtask_action: 'update_subtask',
			new_subtask_name: new_subtask
		}
		$(this).closest('.zpm_subtask_item').find('.zpm_subtask_name').removeAttr('contentEditable');
		$(this).removeClass('is_active');

		ZephyrProjects.update_subtasks(data, function(response){
			ZephyrProjects.notification('Changes saved successfully.');
		});
	});

	/* Mark if subtask is done */
	$('body').on('click', '.zpm_subtask_is_done', function() {
		var task_id = $(this).data('task-id');

		if ($(this).attr('checked')) {
			var data = {
				id: task_id,
				completed: 1
			}

			ZephyrProjects.complete_task(data, function(response){});
			$(this).closest('.zpm_subtask_item').addClass('zpm_task_complete');
		} else {
			var data = {
				id: task_id,
				completed: 0
			}

			ZephyrProjects.complete_task(data, function(response){});
			$(this).closest('.zpm_subtask_item').removeClass('zpm_task_complete');
		}
	});

	/* Open the new category modal */
	$('body').on('click', '#zpm_new_category_btn, #zpm_new_quick_category', function() {
		ZephyrProjects.open_modal('zpm_new_category_modal');
	});

	/* Create a new category */
	$('body').on('click', '#zpm_create_category', function(e){
		
		var name = $(this).closest('#zpm_new_category_modal').find('#zpm_category_name').val();
		var description = $(this).closest('#zpm_new_category_modal').find('#zpm_category_description').val();
		var color = $(this).closest('#zpm_new_category_modal').find('#zpm_category_color').val();
		if (name == '') { return; }

		ZephyrProjects.notification( 'Creating category.' );
		ZephyrProjects.close_modal();
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_name').val('');
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_description').val('');
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_color').val('');
		var data = {
			category_name: name,
			category_description: description,
			category_color: color,
		}

		ZephyrProjects.create_category(data, function(response){
			$('.zpm_category_list').html(response);
		});
	});

	/* Delete a category */
	$('body').on('click', '.zpm_delete_category', function(){
		var category_id = $(this).data('category-id');

		if (confirm('Are you sure you want to permanently delete this category?')) {
			ZephyrProjects.notification('Deleting category...');
			ZephyrProjects.remove_category({
				id: category_id
			}, function(response){
				$('.zpm_category_list').html(response);
			});
		} else {

		}
	});

	/* Edit a category */
	$('body').on('click', '.zpm_category_row', function(e){
		if (e.target.className == 'zpm_delete_category' || e.target.className == 'zpm_delete_category_icon lnr lnr-cross') { return; }
		ZephyrProjects.open_modal('zpm_edit_category_modal');
		var category_id = $(this).data('category-id');
		var category_color = $(this).find('.zpm_category_color').data('zpm-color');
		var category_name = $(this).find('.zpm_category_name').html();
		var category_description = $(this).find('.zpm_category_description').html();

		$('body').find('#zpm_edit_category_modal .zpm_edit_category').attr('data-category-id', category_id);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_name').val(category_name);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_description').val(category_description);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_color').val(category_color);
		$('#zpm_edit_category_color').wpColorPicker();
	});

	/* Update category */
	$('body').on('click', '#zpm_edit_category', function(e){
		var base = $(this).closest('#zpm_edit_category_modal');
		var category_id = base.find('.zpm_edit_category').data('category-id');
		var name = base.find('#zpm_edit_category_name').val();
		var description = base.find('#zpm_edit_category_description').val();
		var color = base.find('#zpm_edit_category_color').val();
		var data = {
			category_id: category_id,
			category_name: name,
			category_description: description,
			category_color: color,
		}

		if (name == '') {
			return;
		}

		ZephyrProjects.notification( 'Saving changes...' );
		ZephyrProjects.close_modal();
		ZephyrProjects.update_category(data, function(response){
			$('.zpm_category_list').html(response);
		});
	});

	/* Text Editor */
	$('.zpm_editor_toolbar a').click(function(e) {
		e.preventDefault();
		var command = $(this).data('command');

		if (command == 'h1' || command == 'h2' || command == 'p') {
			document.execCommand('formatBlock', false, command);
		}

		if (command == 'forecolor' || command == 'backcolor') {
			document.execCommand($(this).data('command'), false, $(this).data('value'));
		}

		if (command == 'createlink' || command == 'insertimage') {
			url = prompt('Enter the link here: ','http:\/\/');
			document.execCommand($(this).data('command'), false, url);
		}

		if (command == 'addCode') {
			document.execCommand("insertHTML", false, "<code class='cca_code_snippet' style='display: block;'>" + document.getSelection() + "</code>");
		} else {
			document.execCommand($(this).data('command'), false, null);
		}
    });

    /* Project quickview tabs */
    $('body').on('click', '.zpm_nav_item', function() {
    	tab_id = $(this).data('zpm-tab');
    	$('body').find('.zpm_nav_item').removeClass('zpm_nav_item_selected');
    	$(this).addClass('zpm_nav_item_selected');
    	$('body').find('.zpm_tab_pane').removeClass('zpm_tab_active');
    	$('body').find('.zpm_tab_pane[data-zpm-tab="' + tab_id + '"]').addClass('zpm_tab_active');
    	
		if (tab_id == "2") {
			$('.project-type-board').addClass('no-background');
		} else {
			$('.project-type-board').removeClass('no-background');
		}
    });

    // Delete a task
	$('body').on('click', '#zpm_delete_task', function(){
		var task_id = $('body').find('#zpm_task_view_id').val();

		ZephyrProjects.remove_task({
			task_id: task_id
		});

		$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').remove();
		if ($('body').find('.zpm_task_list_row').length <= 0) {
			$('.zpm_no_results_message').show();
			$('body').find('#zpm_task_option_container').addClass('zpm_hidden');
			$('body').find('#zpm_task_list_container').addClass('zpm_hidden');
		}

    	ZephyrProjects.close_modal();
	}); 

	$('body').on('click', '.zpm_filter_file', function(){
		var project_id = $(this).data('project-id');
		$('.zpm_filter_file').removeClass('zpm_selected_link');
		$(this).addClass('zpm_selected_link');
		$('body').find('.zpm_file_item_container').hide();
		// If the project has files
		if ($('body').find('.zpm_file_item_container[data-project-id="' + project_id + '"]').length > 0) {
			if (project_id == '-1') {
				$('body').find('.zpm_file_item_container').show();
			} else {
				$('body').find('.zpm_file_item_container[data-project-id="' + project_id + '"]').show();
			}
			$('#zpm_no_files').hide();
		} else {
			$('#zpm_no_files').show();
		}
	});

	/* WP Dashboard Charts */
	var zpm_progress_chart = document.getElementById("zpm-dashboard-project-chart");
	var completed_projects = $('body').find('#zpm-dashboard-project-chart').data('project-completed');
	var pending_projects = $('body').find('#zpm-dashboard-project-chart').data('project-pending');

    var zpm_chart_data = {
	    labels: [
	        "Completed Projects",
	        "Pending Projects"
	    ],
	    datasets: [
	        {
	            data: [completed_projects, pending_projects],
	            backgroundColor: [
	            	'#ec1665',
	                "#14aaf5",
	            ],
	            borderWidth: 0
	        }]
	};
	 
	var chart_options = {
	  cutoutPercentage: 80,
	  legend: {
	    position: 'bottom'
	  },
	  animation: {
	    animateRotate: false,
	    animateScale: true
	  }
	};

	if (zpm_progress_chart !== null) {
		var doughnut_chart = new Chart(zpm_progress_chart, {
		  type: 'doughnut',
		  data: zpm_chart_data,
		  options: chart_options
		});
	}

	function zpm_loader_modal(message) {
		var html = '<div id="zpm_loader_modal" class="zpm-modal active">' + message + '</div>';
		if ($('body').find('#zpm_loader_modal').length > 0) {
			zpm_close_loader_modal();
		}
		$('body').append(html);
	}

	function zpm_close_loader_modal() {
		$('body').find('#zpm_loader_modal').remove();
	}

	$('body').on('click', '#zpm_update_project_progress', function(){
		zpm_update_project_progress();
	});

	function zpm_update_project_progress( project_id ) {
		// Display a project progress chart
		if (typeof project_id == 'undefined') {
			var project_id = $('body').find('#zpm_project_editor').data('project-id');
		}

		var data = {
			project_id: project_id
		}

		ZephyrProjects.project_progress(data, function(response){
			var data = [];
			$(response.chart_data).each(function(e, f){
				data.push({
					date: f.date,
					completed: f.completed_tasks,
					pending: f.pending_tasks,
					overdue: f.overdue_tasks
				});
			});
			ZephyrProjects.project_chart(data);
			zpm_close_loader_modal();
		});
	}

	$('#zpm_load_activities').on('click', function(){
		var button = $(this);
		var offset = button.data('offset');
		var data = {
			offset: offset
		}
		button.data('offset', offset+=1);
		zpm_loader_modal('Loading activity...');

		ZephyrProjects.display_activity(data, function(response){
			$('body').find('#zpm_loader_modal').remove();
			if (response !== false && response !== '') {
				$('#zpm_activity_body').append(response);
			} else {
				button.addClass('disabled').attr('disabled', 'disabled');
				zpm_close_loader_modal();
			}
		});
	});

	// Progress Page
	var project_selector = $('#zpm_project_progress_select');

	if (project_selector.length > 0) {
		var project_id = project_selector.val();
		zpm_update_project_progress( project_id );
		zpm_loader_modal('Loading progress...');
	}

	project_selector.on('change', function(){
		var project_id = $(this).val();
		zpm_update_project_progress( project_id );
		zpm_loader_modal('Loading progress...');
	});

	// Quick Menu
	$('#zpm_new_quick_file').on('click', function(){
		ZephyrProjects.open_modal('zpm_new_file_upload');
	});

	$('#zpm_submit_file').on('click', function(){
		ZephyrProjects.close_modal();
		var attachment = [];
		attachment['attachment_id'] = $('#zpm_uploaded_file_name').val();
		attachment['attachment_type'] = 'project';
		attachment['subject_id'] = $('#zpm_file_upload_project').val();
		upload_attachment(attachment['attachment_id'], attachment['attachment_type'], attachment['subject_id']);
	});

	// File Uploader
	var quick_file_uploader;
	$('#zpm_upload_file').on('click', function() {
		if (quick_file_uploader) {
			quick_file_uploader.open();
			return;
		}

		quick_file_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Select File',
			button: {
			text: 'Select File'
		}, multiple: false });

		quick_file_uploader.on('select', function() {
			var attachment = quick_file_uploader.state().get('selection').first().toJSON();
			$('#zpm_uploaded_file_name').val(attachment.id);
		});
		quick_file_uploader.open();
	});

	$('body').on('click', '#zpm_add_project_to_dashboard', function(){
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var data = {
			project_id: project_id
		}
		ZephyrProjects.add_to_dashboard(data);
	});

	$('body').on('click', '.zpm_remove_project_from_dashboard', function(){
		var project_id = $(this).closest('.zpm_dashboard_project').data('project-id');
		var data = {
			project_id: project_id
		}
		$(this).parents('.zpm_dashboard_project_container').remove();
		ZephyrProjects.remove_from_dashboard(data);
	});

	$('#zpm_dismiss_review_notice').on('click', function(){
		$(this).closest('.zpm_admin_notice').remove();
		var data = {
			notice: 'review_notice'
		}
		ZephyrProjects.dismiss_notice(data);
	});

	$('#zpm_dismiss_welcome_notice').on('click', function(){
		$(this).closest('.zpm_admin_notice').remove();
		var data = {
			notice: 'welcome_notice'
		}
		ZephyrProjects.dismiss_notice(data);
	});

	// Update project status
	$('#zpm_update_project_status').on('click', function(){
		var project_id = $(this).data('project-id');
		var status = $('#zpm_project_status').html();
		var status_color = $('.zpm_project_status.active').data('status');
		var data = {
			project_id: project_id,
			status: status,
			status_color: status_color
		};

		ZephyrProjects.update_project_status(data, function(response){});
	});

	$('.zpm_project_status').on('click', function(){
		$('.zpm_project_status').removeClass('active');
		$(this).addClass('active');
	});

	$('body').on('click', '#zpm_add_custom_field_pro', function(){
		$(this).find('.zpm-pro-notice').toggleClass('active');
	});

	$('body').on('click', '.zpm-close-pro-notice', function(){
		$(this).closest('.zpm-pro-notice').removeClass('active');
	});

	$('body').on('click', '#zpm-save-project-members', function(){
		var members = [];
		var project_id = $('#zpm-project-id').val();

		$('.zpm-project-member').each(function(){
			var checked = $(this).is(':checked');
			var member_id = $(this).data('member-id');
			if (checked) {
				members.push(member_id);
			}
		});

		var data = {
			project_id: project_id,
			members: members
		}

		ZephyrProjects.Project.update_members( data, function(response){
		});
	});

	$('.zpm-can-zephyr').on('change', function(){
		var checked = $(this).is(':checked');
		var userID = $(this).data('user-id');
		ZephyrProjects.updateUserAccess({ user_id: userID, access: checked }, function(res){
			
		});
	});

	$('.zpm-dismiss-whats-new').on('click', function(){
		let noticeContainer = $(this).closest('#zpm-whats-new');
		let notice_id = $(this).closest('#zpm-whats-new').data('notice');

		noticeContainer.addClass( 'zpm-hidden' );
		ZephyrProjects.dismiss_notice({
			notice: notice_id
		});
	});

	$('.zpm-dismiss-notice-button').on('click', function(){
		let noticeContainer = $(this).closest('#zpm-whats-new');
		let notice_id = $(this).data('notice-version')

		noticeContainer.addClass( 'zpm-hidden' );
		ZephyrProjects.dismiss_notice({
			notice: notice_id
		});
	});

	$('[data-zpm-modal-trigger]').on('click', function(){
		let id = $(this).data('zpm-modal-trigger');
		ZephyrProjects.open_modal(id);
	});

	$('#zpm-new-team').on('click', function(){
		let teamName = $('#zpm-new-team-name');
		let teamDescription = $('#zpm-new-team-description');
		let teamMembers = [];

		$('.zpm-new-team-member').each(function(){
			if ($(this).is(':checked')) {
				teamMembers.push($(this).data('member-id'));
			}
			$(this).removeAttr('checked');
		});

		ZephyrProjects.addTeam({ 
			name: teamName.val(),  
			description: teamDescription.val(),
			members: teamMembers
		}, function(res) {
			$('.zpm-teams-list').append(res.html);
			$('#zpm-new-task-team-selection').append('<option value="' + res.team.id + '">' + res.team.name + '</option>');
		});

		teamName.val('');
		teamDescription.val('');
		ZephyrProjects.close_modal('#zpm-new-team-modal');
		$('#zpm-no-teams-notice').hide();
	});

	// Check if new task - team selection has changed
	$('body').on('change', '#zpm-new-task-team-selection', function(){
		let teamId = $(this).val();

		$('#zpm_new_task_assignee > option').show();
        $('#zpm_new_task_assignee').trigger("chosen:updated");
        $('#zpm_new_task_assignee').val('-1');
		ZephyrProjects.getTeam({ 
			id: teamId
		}, function(res) {
			if (res != null) {
				$('#zpm_new_task_assignee > option').hide();
				$('#zpm_new_task_assignee > option[value="-1"]').show();
				$('#zpm_new_task_assignee').trigger("chosen:updated");

				$.each(res.members, function(key, val) {
					let userId = val.id;
					$('#zpm_new_task_assignee > option[value="' + userId + '"]').show();
	        		$('#zpm_new_task_assignee').trigger("chosen:updated");
				});
			}
		});
	});

	$('body').on('click', '.zpm-edit-team', function(){
		let id = $(this).data('team-id');
		let idHidden = $('#zpm-edit-team-id');
		let teamName = $('#zpm-edit-team-name');
		let teamDescription = $('#zpm-edit-team-description');

		$('body').find('.zpm-edit-team-member').removeAttr('checked');
		idHidden.val(id);
		teamName.val('');
		teamDescription.val('');

		ZephyrProjects.open_modal('zpm-edit-team-modal');

		ZephyrProjects.getTeam({ 
			id: id
		}, function(res) {
			teamName.val(res.name);
			teamDescription.val(res.description);
			$.each(res.members, function(key, val) {
				let userId = val.id;
				$('body').find('.zpm-edit-team-member[data-member-id="' + userId + '"]').attr('checked', 'checked');
			});
		});

	});

	$('body').on('click', '.zpm-delete-team', function(){
		let id = $(this).data('team-id');
		let container = $(this).closest('.zpm_team_member');

		ZephyrProjects.confirm('Are you sure you want to delete this team? This action cannot be undone.', function(){
			container.remove();
			ZephyrProjects.deleteTeam({ 
				id: id
			}, function(res) {
			});
		});
	});

	$('#zpm-edit-team').on('click', function(){
		let id = $('#zpm-edit-team-id');
		let teamName = $('#zpm-edit-team-name');
		let teamDescription = $('#zpm-edit-team-description');
		let teamMembers = [];

		$('.zpm-edit-team-member').each(function(){
			if ($(this).is(':checked')) {
				teamMembers.push($(this).data('member-id'));
			}
			$(this).removeAttr('checked');
		});

		ZephyrProjects.updateTeam({ 
			id: id.val(),
			name: teamName.val(),  
			description: teamDescription.val(),
			members: teamMembers
		}, function(res) {
			$('body').find('.zpm_team_member[data-team-id="' + id.val() + '"]').replaceWith(res);
		});

		teamName.val('');
		teamDescription.val('');
		ZephyrProjects.close_modal('#zpm-edit-team-modal');
	});

});