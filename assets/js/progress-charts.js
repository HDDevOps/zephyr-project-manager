jQuery(document).ready(function($){
	ZephyrProjects.dashboard_charts();

	// Progress Page
	$(document).find('#zpm_project_progress_select').chosen({
	    disable_search_threshold: 10,
	    no_results_text: "Oops, no projects found!"
	});
});