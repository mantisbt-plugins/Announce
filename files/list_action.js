// Copyright (c) 2010 John Reese
// Copyright (c) 2017 Damien Regad
// Licensed under the MIT license

jQuery(document).ready(function($) {
	// Message list behaviors
	$("input.announce_select_all").change(function(){
		$("input[name='message_list[]']").prop("checked", $(this).prop("checked"));
	});

	/**
	 * Count the number of non-deleted siblings for the given context row
	 * @param row
	 * @return integer Number of non-deleted siblings
	 */
	function num_siblings(row) {
		// Note: we can't use jQuery's siblings() method, as there can be
		// multiple announcements in the same table. To calculate the number, we
		// go back to the first category row, then back down to the next spacer
		// row, excluding deleted contexts.
		var siblings = row
			.prevUntil('.row-category').addBack()
			.nextUntil('.spacer').addBack()
			.not('.row-deleted');
		var num = siblings.length;

		// If we're removing a newly added context, exclude it from the count
		if(row.hasClass('row-new')) {
			num--;
		}

		return num;
	}

	/**
	 * Display or hide the "last context" warning message
	 * @param message_id Message for which the warning applies
	 * @param show True to show the warning, false to hide it
	 */
	function toggle_last_context_warning(message_id, show) {
		var warning = $('#warning_last_context_' + message_id);
		if(show) {
			warning.show();
		} else {
			warning.hide();
		}
	}

	function delete_action (event){
		var parent_row = $(this).parents("tr");
		var message_id = $(this).data('message-id');

		if ($(this).hasClass("announce_delete_context_added")) {
			var cell = $("td.announce_list_" + message_id);
			cell.prop("rowspan", cell.prop("rowspan") - 1);
			toggle_last_context_warning(message_id, num_siblings(parent_row) === 0);
			parent_row.remove();
			return;
		}

		var context_id = $(this).data("context-id");

		var input_deleted = "input[name='context_delete_"+context_id+"']";
		var input_location = "select[name='location_"+context_id+"']";
		var input_project = "select[name='project_"+context_id+"']";
		var input_access = "select[name='access_"+context_id+"']";
		var input_ttl = "input[name='ttl_"+context_id+"']";
		var input_dismissable = "input[name='dismissable_"+context_id+"']";

		// Mark the row as deleted and disable the form's inputs
		var context_deleted = $(input_deleted).attr("value");
		parent_row.toggleClass('row-deleted');
		if (context_deleted === "0") {
			$(input_deleted).prop("value", "1");
			$(input_location).prop("disabled", "disabled");
			$(input_project).prop("disabled", "disabled");
			$(input_access).prop("disabled", "disabled");
			$(input_ttl).prop("disabled", "disabled");
			$(input_dismissable).prop("disabled", "disabled");
			toggle_last_context_warning(message_id, num_siblings(parent_row) === 0);
		} else {
			$(input_deleted).prop("value", "0");
			$(input_location).prop("disabled", "");
			$(input_project).prop("disabled", "");
			$(input_access).prop("disabled", "");
			$(input_ttl).prop("disabled", "");
			$(input_dismissable).prop("disabled", "");
			toggle_last_context_warning(message_id, false);
		}
	}

	// "deleting" a context from edit list
	$("a.announce_delete_context").click(delete_action);

	// "adding" a new context from edit list
	$("a.announce_add_context").click(function(event){
		var message_id = $(this).data('message-id');
		var categoryrow = $(this).parents("tr");

		$.ajax({
			dataType: "html",
			url: Announce.rest_api('context/') + message_id,
			success: function(data) {
				var row = $("td.announce_list_"+message_id);
				row.prop("rowspan", row.prop("rowspan")+1);

				$(categoryrow).after(data);

				$("a.announce_delete_context_new")
					.click(delete_action)
					.removeClass("announce_delete_context_new")
					.addClass("announce_delete_context_added");

				toggle_last_context_warning(message_id, false);
			}
		});
	});
});
