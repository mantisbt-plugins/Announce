// Copyright (c) 2010 John Reese
// Copyright (c) 2017 Damien Regad
// Licensed under the MIT license

jQuery(document).ready(function($) {

		// Move announcement to the page's top, between navbar and breadcrumbs
		$('div.main-content').prepend( $('div.announcement-header') );

		$("div.announcement img.announcement-dismiss").click(function(event) {
				var div = $(this).parent("div.announcement");
				context_id = $(this).attr("value");

				xhr = $.ajax({
					dataType: "json",
					url: "xmlhttprequest.php?entrypoint=plugin_announce_dismiss&context_id="+context_id,
					success: function(data) {
							if (data == context_id) {
								$(div).fadeOut();
							}
						},
					error: function(xhr, textStatus, errorThrown) {
							console.error(
								'Announcement dismissal failed',
                                { error: errorThrown, request: this.url }
							);
						}
					});
			});

	});
