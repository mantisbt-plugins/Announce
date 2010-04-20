<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

/**
 * Generate HTML row to be inserted while editing an announcement.
 */
function xmlhttprequest_plugin_announce_dismiss_context() {
	$context_id = gpc_get_int("context_id");

	/* todo: mark the context as dismissed in the database */

	# echoing the context ID as "success"
	echo json_encode($context_id);
}

