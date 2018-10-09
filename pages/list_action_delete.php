<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2018 Damien Regad
# Licensed under the MIT license

form_security_validate( 'plugin_Announce_list_action_delete' );
access_ensure_global_level( plugin_config_get( 'manage_threshold' ) );

$t_message_list = gpc_get_int_array( 'message_list', array() );

if( count( $t_message_list ) > 0 ) {
	# Retrieve message names
	$t_messages = AnnounceMessage::load_by_id( $t_message_list, true );
	foreach( AnnounceMessage::clean( $t_messages ) as $t_key => $t_message ) {
		$t_message_names[$t_key] = $t_message->title;
	}

	helper_ensure_confirmed(
		plugin_lang_get( 'action_delete_confirm' )
		. '<br/>'
		. implode( ', ', $t_message_names ),
		plugin_lang_get( 'action_delete' )
	);

	AnnounceMessage::delete_by_id( array_keys( $t_messages ) );
}

form_security_purge( 'plugin_Announce_list_action_delete' );
print_successful_redirect( plugin_page( 'list', true ) );
