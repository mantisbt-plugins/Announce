<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2018 Damien Regad
# Licensed under the MIT license

form_security_validate( 'plugin_Announce_list_action_update' );
access_ensure_global_level( plugin_config_get( 'manage_threshold' ) );

$t_message_list = gpc_get_int_array( 'message_list', array() );

if( count( $t_message_list ) > 0 ) {
	$t_messages = AnnounceMessage::load_by_id( $t_message_list, true );

	foreach( $t_messages as $t_message_id => $t_message ) {
		$t_new_title = gpc_get_string( "title_{$t_message_id}" );
		$t_new_message = gpc_get_string( "message_{$t_message_id}" );

		foreach( $t_message->contexts as $t_context_id => $t_context ) {
			$t_delete = gpc_get_bool( "context_delete_{$t_context_id}" );

			if( $t_delete ) {
				$t_context->_delete = $t_delete;

			}
			else {
				$t_new_location = gpc_get_string( "location_{$t_context_id}" );
				$t_new_project = gpc_get_int( "project_{$t_context_id}" );
				$t_new_access = gpc_get_int( "access_{$t_context_id}" );
				$t_new_ttl = gpc_get_int( "ttl_{$t_context_id}" );
				$t_new_dismissable = gpc_get_bool( "dismissable_{$t_context_id}" );

				if( !is_blank( $t_new_location ) && Announce::isValidLocation( $t_new_location ) ) {
					$t_context->location = $t_new_location;
				}
				if( $t_new_project == 0 || project_exists( $t_new_project ) ) {
					$t_context->project_id = $t_new_project;
				}
				if( $t_new_access >= 0 ) {
					$t_context->access = $t_new_access;
				}
				if( $t_new_ttl >= 0 ) {
					$t_context->ttl = $t_new_ttl;
				}
				$t_context->dismissable = $t_new_dismissable;
			}
		}

		if( !is_blank( $t_new_title ) ) {
			$t_message->title = $t_new_title;
		}
		if( !is_blank( $t_new_message ) ) {
			$t_message->message = $t_new_message;
		}

		$t_message->save();
	}

	$t_new_contexts = gpc_get_int_array( 'context_new', array() );
	if( count( $t_new_contexts ) > 0 ) {
		$t_new_locations = gpc_get_string_array( 'location_new', array() );
		$t_new_projects = gpc_get_int_array( 'project_new', array() );
		$t_new_accesses = gpc_get_int_array( 'access_new', array() );
		$t_new_ttls = gpc_get_int_array( 'ttl_new', array() );
		$new_dismissables = gpc_get_bool_array( 'dismissable_new', array() );

		for( $i = 0; $i < count( $t_new_contexts ); $i++ ) {
			$t_context = new AnnounceContext();
			$t_context->message_id = $t_new_contexts[$i];
			$t_context->project_id = $t_new_projects[$i];
			$t_context->location = $t_new_locations[$i];
			$t_context->access = $t_new_accesses[$i];
			$t_context->ttl = $t_new_ttls[$i];
			$t_context->dismissable = isset( $new_dismissables[$i] )
				? $new_dismissables[$i]
				: false;

			if(    ( $t_context->project_id == 0 || project_exists( $t_context->project_id ) )
				&& ( !is_blank( $t_context->location ) && Announce::isValidLocation( $t_context->location ) )
				&& ( $t_context->access >= 0 && $t_context->ttl >= 0 )
			) {
				$t_context->save();
			}
		}
	}
}

form_security_purge( 'plugin_Announce_list_action_update' );
print_successful_redirect( plugin_page( 'list', true ) );
