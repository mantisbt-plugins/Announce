<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

form_security_validate("plugin_Announce_config");
access_ensure_global_level(config_get("manage_plugin_threshold"));

function maybe_set_option( $name, $value ) {
	if ( $value != plugin_config_get( $name ) ) {
		plugin_config_set( $name, $value );
	}
}

maybe_set_option("manage_threshold", gpc_get_int("manage_threshold"));

form_security_purge("plugin_Announce_config");
print_successful_redirect(plugin_page("config_page", true));

