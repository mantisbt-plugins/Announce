<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

form_security_validate("plugin_Announce_create");
access_ensure_global_level(plugin_config_get("manage_threshold"));

$title = gpc_get_string("title");
$message = gpc_get_string("message");

$message = new AnnounceMessage($title, $message);
$message->save();

print_successful_redirect(plugin_page("list", true));
form_security_validate("plugin_Announce_create");

