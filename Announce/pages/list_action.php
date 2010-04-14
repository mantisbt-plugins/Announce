<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

form_security_validate("plugin_Announce_list_action");
access_ensure_global_level(plugin_config_get("manage_threshold"));

$action = gpc_get_string("action");
$message_list = gpc_get_int_array("message_list", array());

if (count($message_list) < 1) {
	form_security_purge("plugin_Announce_list_action");
	print_header_redirect(plugin_page("list", true));
}

$messages = AnnounceMessage::load_by_id($message_list, true);

function array_object_properties($arr, $prop) {
	$props = array();
	foreach($arr as $key => $obj) {
		$props[$key] = $obj->$prop;
	}
	return $props;
}

### DELETE
if ($action == "delete") {
	$message_names = array_object_properties(AnnounceMessage::clean($messages), "title");
	helper_ensure_confirmed(plugin_lang_get("action_delete_confirm") . "<br/>" . implode(", ", $message_names), plugin_lang_get("action_delete"));
	AnnounceMessage::delete_by_id(array_keys($messages));

	form_security_purge("plugin_Announce_list_action");
	print_successful_redirect(plugin_page("list", true));

}

