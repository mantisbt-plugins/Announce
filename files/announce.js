// Copyright (c) 2010 John Reese
// Copyright (c) 2017 Damien Regad
// Licensed under the MIT license

jQuery(document).ready(function($) {
	var announcement = $('div.announcement');

	// Move announcement to the page's top, between navbar and breadcrumbs
	$('div.main-content').prepend($(announcement));

	// Manual dismissal of announcement (user click)
	$('img.announcement-dismiss').click(dismiss);

    /**
     * AJAX to dismiss an announcement
     */
	function dismiss () {
		var context_id = $(announcement).data('id');

		$.ajax({
			dataType: 'json',
			url: 'xmlhttprequest.php?entrypoint=plugin_announce_dismiss&context_id=' + context_id,
			success: function(data) {
				if (data == context_id) {
					$(announcement).fadeOut();
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
