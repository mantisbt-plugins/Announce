// Copyright (c) 2010 John Reese
// Copyright (c) 2017 Damien Regad
// Licensed under the MIT license

/**
 * Namespace for global function used in list_action.js
 */
var Announce = Announce || {};

/**
 * Return MantisBT REST API URL for given endpoint
 * @param {string} endpoint
 * @returns {string} REST API URL
 */
Announce.rest_api = function(endpoint) {
	// Using the full URL (through index.php) to avoid issues on sites
	// where URL rewriting is not working (#31)
	return "api/rest/index.php/plugins/Announce/" + endpoint;
};

jQuery(document).ready(function($) {
	var announcement = $('div.announcement');

	// Display/hide warning if selected access level is lower than the minimum
	$('#manage_threshold').change(function() {
		if ($(this).data('access-level') > $(this).val()) {
			$('#threshold_warning').show();
		} else {
			$('#threshold_warning').hide();
		}
	});

	// Move announcement to the page's top, between navbar and breadcrumbs
	var main_div = $('div.main-content');
	if (!main_div.length) {
		// Admin pages don't have a main-content div
		main_div = $('div.main-container');
	}
	main_div.prepend($(announcement));

	// Manual dismissal of announcement (user click)
	$('img.announcement-dismiss').click(dismiss);

	// Automatic dismissal based on announcement's time-to-live
	var context_ttl = announcement.data('ttl');
	var timeoutID;
	if (context_ttl > 0) {
		timeoutID = window.setTimeout(dismiss, context_ttl * 1000, announcement);
	}

	/**
	 * AJAX to dismiss an announcement
	 */
	function dismiss () {
		var context_id = $(announcement).data('id');

		// Clear the automatic dismissal timeout if it has been set
		if (context_ttl > 0) {
			clearTimeout(timeoutID);
		}

		$.ajax({
			type: 'POST',
			url: Announce.rest_api('dismiss/') + context_id,
			success: function() {
				$(announcement).fadeOut();
			},
			error: function(xhr, textStatus, errorThrown) {
				console.error(
					'Announcement dismissal failed',
					{ error: errorThrown, request: this.url }
				);
			}
		});
	}

});
