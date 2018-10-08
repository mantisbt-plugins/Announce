<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

form_security_validate("plugin_Announce_create");
access_ensure_global_level(plugin_config_get("manage_threshold"));

$title = gpc_get_string("title");
$message = gpc_get_string("message");

$location = gpc_get_string("location");
$project_id = gpc_get_int("project_id");
$access = gpc_get_int("access");
$ttl = gpc_get_int("ttl");
$dismissable = gpc_get_bool("dismissable");

if (!Announce::isValidLocation( $location ) ) {
	error_parameters( $location );
	plugin_error(AnnouncePlugin::ERROR_UNKNOWN_LOCATION, ERROR);
}

if ($ttl < 0) {
	plugin_error(AnnouncePlugin::ERROR_INVALID_TTL, ERROR);
}

if ($project_id != 0) {
	project_ensure_exists($project_id);
}

$message = new AnnounceMessage($title, $message);

$context = new AnnounceContext();
$context->location = $location;
$context->project_id = $project_id;
$context->access = $access;
$context->ttl = $ttl;
$context->dismissable = $dismissable;

$message->contexts[] = $context;
$message->save();

form_security_purge("plugin_Announce_create");
print_successful_redirect(plugin_page("list", true));

