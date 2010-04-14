// Copyright 2010 (c) John Reese
// Licensed under the MIT license

jQuery(document).ready(function($) {
		// Message list behaviors
		$("input.announce_select_all").change(function(){
				$("input[name='message_list[]']").attr("checked", $(this).attr("checked"));
			});

		function delete_action (event){
				if ($(this).hasClass("announce_delete_context_added")) {
					row = $("td.announce_list_"+$(this).attr("value"));
					row.attr("rowspan", row.attr("rowspan")-1);
					$(this).parents("tr").remove();
					return;
				}

				context_id = $(this).attr("value");
				alert(context_id);

				input_deleted = "input[name='context_delete_"+context_id+"']";
				input_location = "select[name='location_"+context_id+"']";
				input_project = "select[name='project_"+context_id+"']";
				input_access = "select[name='access_"+context_id+"']";
				input_ttl = "input[name='ttl_"+context_id+"']";
				input_dismissable = "input[name='dismissable_"+context_id+"']";

				context_deleted = $(input_deleted).attr("value");

				if (context_deleted == "0") {
					$(input_deleted).attr("value", "1");
					$(input_location).attr("disabled", "disabled");
					$(input_project).attr("disabled", "disabled");
					$(input_access).attr("disabled", "disabled");
					$(input_ttl).attr("disabled", "disabled");
					$(input_dismissable).attr("disabled", "disabled");
				} else {
					$(input_deleted).attr("value", "0");
					$(input_location).attr("disabled", "");
					$(input_project).attr("disabled", "");
					$(input_access).attr("disabled", "");
					$(input_ttl).attr("disabled", "");
					$(input_dismissable).attr("disabled", "");
				}
			}

		// "deleting" a context from edit list
		$("a.announce_delete_context").click(delete_action);

		// "adding" a new context from edit list
		$("a.announce_add_context").click(function(event){
				var message_id = $(this).attr("value");
				var categoryrow = $(this).parents("tr");

				xhr = $.ajax({
					async: false,
					dataType: "html",
					url: "xmlhttprequest.php?entrypoint=plugin_announce_add_context&row=1&message_id="+message_id,
					success: function(data) {
							row = $("td.announce_list_"+message_id);
							row.attr("rowspan", row.attr("rowspan")+1);

							$(categoryrow).after(data);

							$("a.announce_delete_context_new").click(delete_action);
							$("a.announce_delete_context_new").removeClass("announce_delete_context_new").addClass("announce_delete_context_added")
						}
					});
			});
	});

