<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

class AnnouncePlugin extends MantisPlugin {
	function register() {
		$this->name = plugin_lang_get("plugin_title");
		$this->description = plugin_lang_get("plugin_description");

		$this->version = "0.1";
		$this->requires		= array(
			"MantisCore" => "1.2.0",
			"jQuery" => "1.4",
		);

		$this->author		= "John Reese";
		$this->contact		= "jreese@leetcode.net";
		$this->url			= "http://leetcode.net";
	}

	function config() {
		return array(
			"manage_threshold" => MANAGER,
		);
	}

	function hooks() {
		return array(
			"EVENT_CORE_READY" => "api",
			"EVENT_LAYOUT_RESOURCES" => "resources",
			"EVENT_MENU_MANAGE" => "menu_manage",

			"EVENT_LAYOUT_BODY_BEGIN" => "body_begin",
		);
	}

	function api() {
		require_once("api/base.php");
		require_once("api/message.php");
		require_once("api/context.php");
		require_once("api/dismiss.php");
	}

	function resources($event) {
		return '<link rel="stylesheet" type="text/css" href="'.plugin_file("announce.css").'"/>
			<script type="text/javascript" src="'.plugin_file("announce.js").'"></script>';
	}

	function menu_manage($event, $user_id) {
		if (access_has_global_level(plugin_config_get("manage_threshold"))) {
			$page = plugin_page("list");
			$label = plugin_lang_get("list_title");

			return "<a href=\"{$page}\">{$label}</a>";
		}
	}

	function body_begin() {
		Announce::display("header", null, "announcement-header");
	}

	function schema() {
		return array(
			# 2010-04-08
			array( "CreateTableSQL", array( plugin_table( "message" ), "
				id			I		NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				timestamp	I		NOTNULL UNSIGNED,
				title		C(100)	NOTNULL,
				message		XL		NOTNULL
				",
				array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
			array( "CreateTableSQL", array( plugin_table( "context" ), "
				id			I		NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				message_id	I		NOTNULL UNSIGNED,
				project_id	I		NOTNULL UNSIGNED,
				location	C(20)	NOTNULL,
				access		I		NOTNULL UNSIGNED,
				ttl			I		NOTNULL UNSIGNED DEFAULT \" '0' \",
				dismissable	L		NOTNULL
				",
				array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
			array( "CreateTableSQL", array( plugin_table( "dismissed" ), "
				context_id	I		NOTNULL UNSIGNED PRIMARY,
				user_id		I		NOTNULL UNSIGNED PRIMARY
				",
				array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),
			#2010-04-14
			array( "CreateIndexSQL", array( "idx_plugin_announce_context", plugin_table( "context" ), "message_id, project_id, location", array( "UNIQUE" ) ) ),
		);
	}
}

