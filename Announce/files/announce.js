// Copyright 2010 (c) John Reese
// Licensed under the MIT license

jQuery(document).ready(function($) {

		$("div.announcement img.announcement-dismiss").click(function(event) {
				var div = $(this).parent("div.announcement");
				context_id = $(this).attr("value");

				xhr = $.ajax({
					async: false,
					dataType: "json",
					url: "xmlhttprequest.php?entrypoint=plugin_announce_dismiss&context_id="+context_id,
					success: function(data) {
							if (data == context_id) {
								$(div).fadeOut();
							}
						}
					});
				
			});

	});
