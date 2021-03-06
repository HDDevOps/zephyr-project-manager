(function($){

	var task_loading_ajax = null;

	ZephyrProjects = {};
	ZephyrProjects.Project = {};
	ZephyrProjects.Task = {};

	ZephyrProjects.notification = function( string, infinite, time ) {
		var infinite = (infinite) ? true : false;
		var current_notification = $(document).find('#zpm_system_notification');
		var time = (time) ? time : 2000;
		var notification = '<div id="zpm_system_notification">' + string + '</div>';

		if ( current_notification.length !== 0 ) {
			current_notification.html(string);
		} else {
			$('body').append(notification);
		}

		if (!infinite) {
			setTimeout( function() {
				$('body').find('#zpm_system_notification').addClass('zpm_hide_notification');
				setTimeout( function() {
					$('body').find('#zpm_system_notification').remove();
				}, 800 );
			}, time );
		}
	}

	ZephyrProjects.modal = function(id, content, active) {
		var html = '<div id="' + id + '" class="zpm-modal">' + content + '</div>';
		$('body').append(html);
		if (active) {
			ZephyrProjects.open_modal(id);
		}
	}

	ZephyrProjects.open_modal = function(selector) {
		$('body').find('#zpm_modal_background').addClass('active');
		$('body').find('#' + selector).addClass('active');
	}

	ZephyrProjects.close_modal = function(selector) {
		if (selector) {
			$('body').find(selector).removeClass('active');
		} else {
			$('body').find('.zpm-modal').removeClass('active');
		}
		$('body').find('#zpm_modal_background').removeClass('active');
		$('body').find('.zpm_modal_background').removeClass('active');
    	$('body').find('.zpm-modal[data-modal-action="remove"]').each(function(){
    		$(this).remove();
    	});
	}

	ZephyrProjects.close_submodal = function(selector) {
		if (selector) {
			$('body').find(selector).removeClass('active');
		} else {
			$('body').find('.zpm-modal').removeClass('active');
		}
	}

	ZephyrProjects.remove_modal = function(selector) {
		$('body').find('.zpm_modal_background').removeClass('active');
		$('body').find('#' + selector).remove();
	}

	ZephyrProjects.get_task = function(data, callback) {
		data.action = 'zpm_get_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				alert( zpm_localized.strings.error_loading_tasks );
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.submit_deactivation_survey = function( data, callback ) {
		data.action = 'zpm_deactivation_survey';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.get_tasks = function(data, callback) {
		data.action = 'zpm_get_tasks';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				alert( zpm_localized.strings.error_loading_tasks );
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.get_all_tasks = function( callback) {
		var data = {};
		data.action = 'zpm_get_all_tasks';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.create_task = function(data, callback) {
		ZephyrProjects.notification( zpm_localized.strings.creating_task );

		data.action = 'zpm_new_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		if ( task_loading_ajax != null ) {
            task_loading_ajax.abort();
            task_loading_ajax = null;
        }

		task_loading_ajax = $.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_task );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.task_created + ': ' + response.name);
				callback(response);
			}
		});
	}

	/**
	* Updates a selected task
	*/

	ZephyrProjects.update_task = function(data, callback) {
		data.action = 'zpm_save_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.task_updated );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.task_updated );
				callback(response);
			}
		});	
	}

	ZephyrProjects.view_task = function(data, callback){
		data.action = 'zpm_view_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_viewing_task );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	/**
	* Copies a project into a new project
	*/
	ZephyrProjects.copy_project = function(data, callback) {
		data.action = 'zpm_copy_project';
		data.wp_nonce = zpm_localized.wp_nonce;
		ZephyrProjects.notification( zpm_localized.strings.copying_project );

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_copying_project );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_copied + ': "' + data.project_name + '"');
				callback(response);
			}
		});	
	}

	/**
	* Initializes the calender and adds all tasks
	*/
	ZephyrProjects.initialize_calendar = function(){
		var tasks = [];

		ZephyrProjects.get_all_tasks(function(data){

			$.each( data, function( key, val ) {
				console.log(val);
				var url = zpm_localized.is_admin ? zpm_localized.tasks_url + '&action=view_task&task_id=' + val.id : zpm_localized.manager_home + '?action=task&id=' + val.id;
				var completed = (val.completed !== '0') ? 'completed' : 'not-completed';
				tasks.push({ 
					title: val.name + '\n' + val.description,
					start: val.date_start,
					end: val.date_due,
					className: completed,
					url: url
				});
			});

			$('#zpm_calendar').fullCalendar({
				header: {
				    right: 'month, agendaWeek, today prev,next'
				  },
				events: tasks
			}); 
			$('body').find('.zpm_task_loader').remove();
		});
		
		// $.getJSON( zpm_localized.rest_url + 'tasks', function( data ) {
		// 	$.each( data, function( key, val ) {
		// 		var url = zpm_localized.is_admin ? zpm_localized.tasks_url + '&action=view_task&task_id=' + val.id : zpm_localized.manager_home + '?action=task&id=' + val.id;
		// 		var completed = (val.completed !== '0') ? 'completed' : 'not-completed';
		// 		tasks.push({ 
		// 			title: val.name + '\n' + val.description,
		// 			start: val.date_start,
		// 			end: val.date_due,
		// 			className: completed,
		// 			url: url
		// 		});
		// 	});
		// }).success(function() { 
		// 	$('#zpm_calendar').fullCalendar({
		// 		header: {
		// 		    right: 'month, agendaWeek, today prev,next'
		// 		  },
		// 		events: tasks
		// 	}); 
		// 	$('body').find('.zpm_task_loader').remove();
		// }).error(function() {
		// 	$('#zpm_calendar').fullCalendar({
		// 		header: {
		// 		    right: 'month, agendaWeek, today prev,next'
		// 		  }
		// 	}); 
		// 	$('body').find('.zpm_task_loader').remove();
		// }).complete(function() {
		// 	$('body').find('.zpm_task_loader').remove();
		// });
	}

	ZephyrProjects.task_reminders = function(){
		var tasks = [];

		// $.getJSON( zpm_localized.rest_url + 'tasks', function(data) {
		// 	$.each( data, function( key, val ) {
		// 		var name = val.name;
		// 		var id = val.id;
		// 		var date_due = val.date_due;
		// 		const now = new Date();
		// 		var parts = date_due.split('-');
		// 		parts[2] = parts[2].split(' ');
		// 		parts[2] = parts[2][0];

		// 		if (val.completed == "1") {
		// 			return;
		// 		}
				
		// 		var mydate = new Date(parts[0], parts[1] - 1, parts[2]);

		// 		if (val.assignee == zpm_localized.user_id) {
		// 			if ((mydate.getFullYear() == now.getFullYear()) &&
		// 				(mydate.getMonth() == now.getMonth()) &&
		// 				(mydate.getDay() == now.getDay()) && !localStorage.getItem('task' + id)) {
		// 					ZephyrProjects.task_notification( zpm_localized.strings.task_due_today + ': ' + name, id, 'task');
		// 			} else if ((mydate.getFullYear() == now.getFullYear()) &&
		// 				(mydate.getMonth() == now.getMonth()) &&
		// 				(mydate.getDay() == now.getDay()+1) && !localStorage.getItem('taskReminder' + id)) {
		// 				ZephyrProjects.task_notification( zpm_localized.strings.task_due_tomorrow + ': "' + name + '"', id, 'taskReminder');
		// 			}
		// 		}
		// 	});

		// });

		ZephyrProjects.get_all_tasks( function( data ) {
			$.each( data, function( key, val ) {
				var name = val.name;
				var id = val.id;
				var date_due = val.date_due;
				const now = new Date();
				var parts = date_due.split('-');
				parts[2] = parts[2].split(' ');
				parts[2] = parts[2][0];

				if (val.completed == "1") {
					return;
				}
				
				var mydate = new Date(parts[0], parts[1] - 1, parts[2]);

				if (val.assignee == zpm_localized.user_id) {
					if ((mydate.getFullYear() == now.getFullYear()) &&
						(mydate.getMonth() == now.getMonth()) &&
						(mydate.getDay() == now.getDay()) && !localStorage.getItem('task' + id)) {
							ZephyrProjects.task_notification( zpm_localized.strings.task_due_today + ': ' + name, id, 'task');
					} else if ((mydate.getFullYear() == now.getFullYear()) &&
						(mydate.getMonth() == now.getMonth()) &&
						(mydate.getDay() == now.getDay()+1) && !localStorage.getItem('taskReminder' + id)) {
						ZephyrProjects.task_notification( zpm_localized.strings.task_due_tomorrow + ': "' + name + '"', id, 'taskReminder');
					}
				}
			});
		});
	}

	ZephyrProjects.task_notification = function(message, id, item){
		var count = $('body').find('.zpm_floating_notification').length;
		var last = $('body').find('.zpm_floating_notification').last();

		var notification = $('body').find('.zpm_floating_notification');
		var height = last.height();

		if (count > 0) {
			var position = last.offset();
			offset = $(window).height() - height;

		    $(window).scroll(function () {
		       var position = last.offset();
		       offset = position.top - height;
		    });

		}
		
		offset = count * 95;
		var notification_holder = $('body').find('#zpm_notifcation_holder');

		setTimeout(function(){
			notification_holder.prepend('<div class="zpm_floating_notification" style="margin-bottom: ' + offset + 'px;" data-id="' + id + '" data-item="' + item + '">Hi ' + zpm_localized.user_name + '<br/>' + message + '<button class="zpm_floating_notification_button">' + zpm_localized.strings.dismiss_notice + '</button></div>');
		},500);
	}

	/**
	* Updates the selected project
	*/
	ZephyrProjects.update_project = function(data, callback){
		data.action = 'zpm_save_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_saving_task );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.changes_saved );
				callback(response);
			}
		});	
	};

	/**
	* Returns the data for a project
	*/
	ZephyrProjects.get_project = function(data, callback){
		data.action = 'zpm_get_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	};

	ZephyrProjects.project_modal = function(subject, header, content, buttons, modal_id) {
		var project_navigation = '<nav class="zpm_nav"><ul class="zpm_nav_list"><li class="zpm_nav_item zpm_nav_item_selected" data-zpm-tab="1">' + zpm_localized.strings.overview + '</li><li class="zpm_nav_item" data-zpm-tab="2">' + zpm_localized.strings.tasks + '</li><li class="zpm_nav_item" data-zpm-tab="3">' + zpm_localized.strings.discussion + '</li></ul></nav>';
		var selectAssignee = '<div id="zpm_quicktask_assignee"><select id="zpm_quicktask_select_assignee" class="zpm_dropdown"><option value="-1">' + zpm_localized.strings.select_assignee + '</option>'; 

		for (var i = 0; i < zpm_localized.users.length; i++) {
			selectAssignee = selectAssignee + '<option value="' + zpm_localized.users[i].data.ID + '" class="zpm_select_option">' + zpm_localized.users[i].data.display_name + '</option>';
		}

		selectAssignee = selectAssignee + '</select></div>';
		
		var newTask = 	'<div class="zpm_quicktask_container">' +
							'<div>' +
								'<div class="zpm_quicktask_content">'+
									'<input id="zpm_quicktask_name" class="zpm_input" type="text" placeholder="' + zpm_localized.strings.name + '"/>' +
									'<textarea id="zpm_quicktask_description" class="zpm_input" type="text" placeholder="' + zpm_localized.strings.description + '"></textarea>' +
									'<input id="zpm_quicktask_date" class="zpm_input" type="text" placeholder="' + zpm_localized.strings.due_date + '" />' +
									selectAssignee +
								'</div>' +
								'<div class="zpm_quicktask_actions"><button id="zpm_create_quicktask" class="zpm_button_outline">' + zpm_localized.strings.save_task + '</button></div>' +
							'</div>' +
						'</div>';

		var tab_1 = '<div id="zpm-project-modal-overview" class="zpm_tab_pane zpm_tab_active" data-zpm-tab="1">' + zpm_localized.strings.loading + '</div>';

		var tab_2 = '<div class="zpm_tab_pane" data-zpm-tab="2" id="zpm_tasks_tab"><button id="zpm_quick_task_add" class="zpm_button_outline">' + zpm_localized.strings.add_task + '</button>' + 
					newTask +
					'<div class="zpm_modal_content"><div class="zpm_task_loader"></div></div></div>';
		var tab_3 = '<div class="zpm_tab_pane" data-zpm-tab="3" id="zpm-project-modal-discussion">' +
		'</div>';

		var modal = '<div id="' + modal_id + '" class="zpm-modal" data-modal-action="remove">' +
					'<div class="zpm_modal_body">' +
						'<h2>' + subject + '</h2>' +
						'<h3>' + header + '</h3><span class="zpm_close_modal">+</span>' +
						'<div class="zpm_modal_actions">' + project_navigation + '</div>' +
						tab_1 +
						tab_2 +
						tab_3 +
						'<div class="zpm_modal_buttons">' + buttons + '</div>' + 
					'</div>' +
				'</div';

		$('body').append(modal);
		$('body').find('.zpm_quicktask_content #zpm_quicktask_select_assignee').chosen({
		    disable_search_threshold: 10,
		    no_results_text: zpm_localized.strings.no_users_found,
		    width: "100%"
		});
		$('body').find('.zpm_quicktask_content #zpm_quicktask_date').datepicker({dateFormat: 'yy-mm-dd' });
		ZephyrProjects.open_modal(modal_id);
	}

	ZephyrProjects.create_project = function(data, callback) {
		data.action = 'zpm_new_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		ZephyrProjects.notification( zpm_localized.strings.creating_project, true);

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_project );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_created + ': "' + data.project_name + '"' );
				callback(response);
			}
		});
	}

	// Removes a message
	ZephyrProjects.remove_comment = function(data, callback) {
		data.action = 'zpm_remove_comment';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_removing_message );
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Marks a task as liked
	ZephyrProjects.like_task = function(data) {
		data.action = 'zpm_like_task';
		data.wp_nonce = zpm_localized.wp_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
			}
		});
	}

	// Copies a task
	ZephyrProjects.copy_task = function(data, callback) {
		data.action = 'zpm_copy_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_copying_task );
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Converts a task to a project
	ZephyrProjects.task_to_project = function(data, callback) {
		data.action = 'zpm_convert_to_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		ZephyrProjects.notification( zpm_localized.strings.converting_to_project );

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_converting_task );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.new_project_created + ': ' + data.project_name );
				callback(response);
			}
		});	
	}

	// Marks a task as complete/incomplete
	ZephyrProjects.complete_task = function(data, callback) {
		data.action = 'zpm_update_task_completion';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Follow a task
	ZephyrProjects.follow_task = function(data, callback) {
		data.action = 'zpm_follow_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports a single task to CSV/JSON
	ZephyrProjects.export_task = function(data, callback) {
		data.action = 'zpm_export_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_task );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports all task to CSV/JSON
	ZephyrProjects.export_tasks = function(data, callback) {
		data.action = 'zpm_export_tasks';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_tasks );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Dismiss a notice
	ZephyrProjects.dismiss_notice = function(data) {
		data.action = 'zpm_dismiss_notice';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response){
			},
			success: function(response){
			}
		});
	}

	// Add project to dashboard
	ZephyrProjects.add_to_dashboard = function(data) {
		data.action = 'zpm_add_project_to_dashboard';
		data.wp_nonce = zpm_localized.wp_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_adding_to_dashboard );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.added_to_dashboard );
			}
		});
	}

	// Remove project from dashboard
	ZephyrProjects.remove_from_dashboard = function(data) {
		ZephyrProjects.notification( zpm_localized.strings.adding_to_dashboard );
		data.action = 'zpm_remove_project_from_dashboard';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.problem_occurred );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.removed_from_dashboard );
			}
		});
	}

	// Display activity
	ZephyrProjects.display_activity = function(data, callback) {
		data.action = 'zpm_display_activities';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Get project progress
	ZephyrProjects.project_progress = function(data, callback) {
		data.action = 'zpm_project_progress';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_loading_project_tasks );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Remove a task
	ZephyrProjects.remove_task = function(data) {
		data.action = 'zpm_remove_task';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_task );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.task_deleted );
			}
		});	
	}

	// Update a category
	ZephyrProjects.update_category = function(data, callback) {
		data.action = 'zpm_update_category';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_saving_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_saved );
				callback(response);
			}
		});
	}

	// Remove category
	ZephyrProjects.remove_category = function(data, callback) {
		data.action = 'zpm_remove_category';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_deleted );
				callback(response);
			}
		});	
	}

	// Uploads tasks
	ZephyrProjects.upload_tasks = function(data, callback) {
		data.action = 'zpm_upload_tasks';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_importing_file );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Deletes a project
	ZephyrProjects.delete_project = function(data, callback) {
		data.action = 'zpm_remove_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_project );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project + ' <i><b>\'' + data.project_name + '\'</b></i> ' + zpm_localized.strings.deleted + '.' );
				callback(response);
			}
		});
	}

	// Filters a task
	ZephyrProjects.filter_tasks = function(data, callback) {
		data.action = 'zpm_filter_tasks';
		data.wp_nonce = zpm_localized.wp_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_filtering );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Creates a category
	ZephyrProjects.create_category = function(data, callback) {
		data.action = 'zpm_create_category';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_created );
				callback(response);
			}
		});
	}

	// Likes a project
	ZephyrProjects.like_project = function(data, callback) {
		data.action = 'zpm_like_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports a project to CSV/JSON
	ZephyrProjects.export_project = function(data, callback) {
		data.action = 'zpm_export_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_project_csv );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Sends a comment
	ZephyrProjects.send_comment = function(data, callback) {
		data.action = 'zpm_send_comment';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_sending_message );
				$('#zpm_task_chat_comment').removeClass('zpm_message_sending').html( zpm_localized.strings.comment );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Updates a subtask
	ZephyrProjects.update_subtasks = function(data, callback) {
		data.action = 'zpm_update_subtasks';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.problem_occurred );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Prints a project
	ZephyrProjects.print_project = function(data, callback) {
		data.action = 'zpm_print_project';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_printing_tasks );
			},
			success: function(response) {

				var projectPage = '<div id="zpm_print_project_page">' +
					'<h2 class="zpm_print_project_heading">' +  response['project'].name + '</h2>' +
					'<h3 class="zpm_print_subheading">' + zpm_localized.strings.printed_from_zephyr + '</h3>' +
					'<div class="zpm_print_project_tasks">' +
						'<ul class="zpm_print_project_task_list">';
								for (var i = 0; i < response['tasks'].length; i++) {
									var due_date = (response['tasks'][i].date_due !== '0000-00-00') ? 'Due: ' + response['tasks'][i].date_due : zpm_localized.strings.no_date_set
									var checked = (response['tasks'][i].completed == '1') ? 'checked' : '';
									projectPage = projectPage + '<li class="zpm_print_project_task">' +
										'<input type="checkbox" class="zpm_print_project_check" value="1" ' + checked + ' />' +
										'<span class="zpm_print_task_assignee">' + response['tasks'][i].username.display_name + ': </span>' +
										'<span class="zpm_print_task_name">' + response['tasks'][i].name + '</span>' +
										'<span class="zpm_print_task_due_date">' + due_date + '</span>' +
									'</li>';
								}
						projectPage = projectPage + '</ul>' +
					'</div>' +
				'</div>';

				setTimeout(function(){
					var printContents = projectPage;
					var originalContents = document.body.innerHTML;
					document.body.innerHTML = printContents;
					window.print();
					document.body.innerHTML = originalContents;
				}, 500);
				callback(response);
			}
		});
	}

	ZephyrProjects.project_chart = function(data) {
		var zpm_progress_chart = document.getElementById('zpm_project_progress_chart');
		var overdue_data = [];
		var pending_data = [];
		var completed_data = [];
		var x_labels = [];

		for (var i = 0; i < data.length; i++) {
			completed_data.push(data[i].completed);
			overdue_data.push(data[i].overdue);
			pending_data.push(data[i].pending);
			x_labels.push(data[i].date);
		}

		var complete_tasks = {
	    	label: zpm_localized.strings.completed_tasks,
	        data: completed_data,
	        borderColor: "rgba(20, 170, 245, .7)",
	        backgroundColor: "rgba(20, 170, 245, .4)",
	        fill: true
	    };

		var pending_tasks = {
	    	label: zpm_localized.strings.pending_tasks,
	        data: pending_data,
	        borderColor: "rgba(110, 206, 252, .9)",
	        backgroundColor: "rgba(110, 206, 252, .7)",
	        fill: true
	    };

		var due_tasks = {
	    	label: zpm_localized.strings.due_tasks,
	        data: overdue_data,
	        borderColor: "rgba(250, 145, 145, .8)",
	        backgroundColor: "rgba(250, 145, 145, .8)",
	        fill: false
	    };

	    var zpm_chart_data = {
	    	labels: x_labels,
		    datasets: [due_tasks, complete_tasks, pending_tasks]
		};
		 
		var chart_options = {
		  legend: {
		    position: 'bottom'
		  },
		  animation: {
		    animateRotate: false,
		    animateScale: true
		  },
		};

		if (zpm_progress_chart !== null) {
			var line_chart = new Chart(zpm_progress_chart, {
			  type: 'line',
			  data: zpm_chart_data,
			  options: chart_options
			});
		}
	}

	ZephyrProjects.update_project_status = function(data, callback){
		data.action = 'zpm_update_project_status';
		data.wp_nonce = zpm_localized.wp_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_updating_status );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_status_saved );
				callback(response);
			}
		});
	}

	ZephyrProjects.Project.update_members = function(data, callback){
		data.action = 'zpm_update_project_members';
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.members_saved );
				callback(response);
			}
		});
	}

	ZephyrProjects.zpm_modal = function(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {
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

	// Initializes the dashboard charts
	ZephyrProjects.dashboard_charts = function() {
		$('.zpm-dashboard-project-chart').each(function(){
			var zpm_progress_chart = $(this)[0];
			var chart_data = JSON.parse(JSON.stringify($(this).data('chart-data')));
			var data = [];
			var overdue_data = [];
			var pending_data = [];
			var completed_data = [];
			var x_labels = [];


			$(chart_data).each(function(e, f){
				data.push({
					date: f.date,
					completed: f.completed_tasks,
					pending: f.pending_tasks,
					overdue: f.overdue_tasks
				});
			});

			for (var i = 0; i < data.length; i++) {
				completed_data.push(data[i].completed);
				overdue_data.push(data[i].overdue);
				pending_data.push(data[i].pending);
				x_labels.push(data[i].date);
			}

			var complete_tasks = {
		    	label: zpm_localized.strings.completed_tasks,
		        data: completed_data,
		        borderColor: "rgba(20, 170, 245, .4)",
		        backgroundColor: "rgba(20, 170, 245, .4)",
		        fill: false
		    };

			var pending_tasks = {
		    	label: zpm_localized.strings.pending_tasks,
		        data: pending_data,
		        borderColor: "rgba(110, 206, 252, .7)",
		        backgroundColor: "rgba(110, 206, 252, .7)",
		        fill: false
		    };

			var due_tasks = {
		    	label: zpm_localized.strings.due_tasks,
		        data: overdue_data,
		        borderColor: "rgba(250, 145, 145, .8)",
		        backgroundColor: "rgba(250, 145, 145, .8)",
		        fill: false
		    };

		    var zpm_chart_data = {
		    	labels: x_labels,
			    datasets: [due_tasks, complete_tasks, pending_tasks]
			};
			 
			var chart_options = {
			  legend: {
			    position: 'bottom'
			  },
			  animation: {
			    animateRotate: false,
			    animateScale: true
			  },
			};

			if (zpm_progress_chart !== null) {
				var line_chart = new Chart(zpm_progress_chart, {
				  type: 'line',
				  data: zpm_chart_data,
				  options: chart_options
				});
			}
		});
	}

	ZephyrProjects.Task.filterBy = function( data, callback ) {
		data.action = 'zpm_filter_tasks_by';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.updateUserAccess = function( data, callback ) {
		data.action = 'zpm_update_user_access';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.addTeam = function( data, callback ) {
		data.action = 'zpm_add_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.updateTeam = function( data, callback ) {
		data.action = 'zpm_update_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.getTeam = function( data, callback ) {
		data.action = 'zpm_get_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.deleteTeam = function( data, callback ) {
		data.action = 'zpm_delete_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.confirm = function( message, confirmCallback ) {
		if ( confirm( message ) ) {
			confirmCallback();
		} else {

		}
	}

	ZephyrProjects.sendDesktopNotification = function( title, body, icon ) {
		if (!("Notification" in window)) {
			alert("This browser does not support desktop notification");
		}
		else if (Notification.permission === "granted") {
			var options = {
			    body: body,
			    icon: icon,
			    dir : "ltr"
			};
			var notification = new Notification( title, options );
		}
		else if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {
				if (!('permission' in Notification)) {
					Notification.permission = permission;
				}

				if (permission === "granted") {
					var options = {
					    body: body,
					    icon: icon,
					    dir : "ltr"
					};

					var notification = new Notification( title, options );
				}
			});
		}
	}

})(jQuery)