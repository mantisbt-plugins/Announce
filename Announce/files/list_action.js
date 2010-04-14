// Copyright 2010 (c) John Reese
// Licensed under the MIT license

jQuery(document).ready(function($) {
		// Message list behaviors
		$("input.announce_select_all").change(function(){
				$("input[name='message_list[]']").attr("checked", $(this).attr("checked"));
			});
	});

