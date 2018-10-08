<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

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

		if( is_array( $p_id ) ) {
			$t_ids = array_filter( $p_id, 'is_int' );
			if (count($t_ids) < 1) {
				return;
			}
			$t_ids = implode( ',', $t_ids );

			$t_where = "IN ({$t_ids})";
			$t_param = null;
		} else {
			$t_where = "= " . db_param();
			$t_param = array( (int)$p_id );
		}

		$t_query = "DELETE FROM {$t_dismissed_table}
			WHERE context_id IN (
				SELECT id 
				FROM {$t_context_table}
				WHERE message_id $t_where
			)";
		db_query( $t_query, $t_param );
	}
}

