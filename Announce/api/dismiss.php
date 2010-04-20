<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

/**
 * Generate HTML row to be inserted while editing an announcement.
 */
function xmlhttprequest_plugin_announce_dismiss() {
	plugin_push_current("Announce");

	$context_id = gpc_get_int("context_id");
	$user_id = auth_get_current_user_id();

	# make sure the message context actually exists
	$context = AnnounceContext::load_by_id($context_id);

	if ($context !== null && $context->dismissable) {
		$dismissed_table = plugin_table("dismissed");

		# check for existing dismissal
		$query = "SELECT * FROM {$dismissed_table} WHERE context_id=".db_param()." AND user_id=".db_param();
		$result = db_query_bound($query, array($context_id, $user_id));

		if (db_num_rows($result) < 1) {
			$query = "INSERT INTO {$dismissed_table} (context_id, user_id) VALUES (".db_param().", ".db_param().")";
			$result = db_query_bound($query, array($context_id, $user_id));
		}

		# echoing the context ID as "success"
		echo json_encode($context_id);
	}

	plugin_pop_current();
}

