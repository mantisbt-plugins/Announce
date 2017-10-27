<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

/**
 * Generate HTML row to be inserted while editing an announcement.
 */
function xmlhttprequest_plugin_announce_dismiss() {
	plugin_push_current("Announce");
	$timestamp = time();

	$context_id = gpc_get_int("context_id");
	$user_id = auth_get_current_user_id();

	# make sure the message context actually exists
	$context = AnnounceContext::load_by_id($context_id);

	# Dismiss announcement if dismissable or a time-to-live has been set
	if ($context && ( $context->dismissable || $context->ttl > 0 ) ) {
		$dismissed_table = plugin_table("dismissed");

		# check for existing dismissal
		$query = "SELECT * FROM {$dismissed_table} WHERE context_id=".db_param()." AND user_id=".db_param();
		$result = db_query($query, array($context_id, $user_id));

		if (db_num_rows($result) < 1) {
			$query = "INSERT INTO {$dismissed_table} (context_id, user_id, timestamp) VALUES (".db_param().", ".db_param().", ".db_param().")";
			$result = db_query($query, array($context_id, $user_id, $timestamp));
		} else  {
			$query = "UPDATE {$dismissed_table} SET timestamp = ".db_param()." WHERE context_id=".db_param()." AND user_id=".db_param();
			$result = db_query($query, array($timestamp, $context_id, $user_id, ));
		}

		# echoing the context ID as "success"
		echo json_encode($context_id);
	}

	plugin_pop_current();
}

class AnnounceDismissed {
	/**
	 * Delete dismissals for the given Context ID.
	 *
	 * @param int|array $p_id Context ID
	 * @return void
	 */
	public static function delete_by_context_id( $p_id ) {
		$t_dismissed_table = plugin_table( 'dismissed' );
		$t_query = "DELETE FROM {$t_dismissed_table} WHERE context_id ";

		if( is_array( $p_id ) ) {
			$t_ids = array_filter( $p_id, 'is_int' );
			if (count($t_ids) < 1) {
				return;
			}
			$t_ids = implode( ',', $t_ids );

			$t_query .= "IN ({$t_ids})";
			db_query( $t_query );
		} else {
			$t_query .= "= " . db_param();
			db_query( $t_query, array( (int)$p_id ) );
		}
	}

	/**
	 * Delete dismissals for the given Message ID.
	 *
	 * @param int|array $p_id Message ID
	 * @return void
	 */
	public static function delete_by_message_id( $p_id ) {
		$t_dismissed_table = plugin_table( 'dismissed' );
		$t_context_table = plugin_table( 'context' );
		$t_query = "DELETE d.* 
				FROM {$t_dismissed_table} d
				JOIN {$t_context_table} c ON c.id = d.context_id
				WHERE c.message_id ";

		if( is_array( $p_id ) ) {
			$t_ids = array_filter( $p_id, 'is_int' );
			if (count($t_ids) < 1) {
				return;
			}
			$t_ids = implode( ',', $t_ids );

			$t_query .= "IN ({$t_ids})";
			db_query( $t_query );
		} else {
			$t_query .= "= " . db_param();
			db_query( $t_query, array( (int)$p_id ) );
		}
	}
}

