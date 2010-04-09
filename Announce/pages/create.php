<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

form_security_validate("plugin_Announce_create");
access_ensure_global_level(plugin_config_get("manage_threshold"));

$title = gpc_get_string("title");
$message = gpc_get_string("message");

$message = new AnnounceMessage($title, $message);
$message->save();

form_security_purge("plugin_Announce_create");
print_successful_redirect(plugin_page("list", true));

