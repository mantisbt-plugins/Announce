// Copyright (c) 2010 John Reese
// Copyright (c) 2017 Damien Regad
// Licensed under the MIT license

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
	$('div.main-content').prepend($(announcement));

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
			dataType: 'json',
			url: 'xmlhttprequest.php?entrypoint=plugin_announce_dismiss&context_id=' + context_id,
			success: function(data) {
				if (data === context_id) {
					$(announcement).fadeOut();
				} else {
					console.error(
						'Unexpected output received from announcement dismissal',
						{ output: data, request: this.url }
					)
				}
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
