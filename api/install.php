<?php
# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

/**
 * Install UpdateFunction to delete orphaned dismissals.
 *
 * Plugin versions < 2.2.0 did not delete dismissals when the parent message
 * or context records were deleted, resulting in orphaned records in the
 * dismissed table.
 *
 * @return int 2 if success
 */
function install_delete_orphans_dismissals() {
	$t_dismissed_table = plugin_table( 'dismissed' );
	$t_context_table = plugin_table( 'context' );

	$t_query = "DELETE d.* 
		FROM {$t_dismissed_table} d
		LEFT JOIN {$t_context_table} c on c.id = d.context_id 
		WHERE c.id IS NULL";
	if( db_query( $t_query ) === false ) {
		return false;
	};

	return 2; // Success
}
