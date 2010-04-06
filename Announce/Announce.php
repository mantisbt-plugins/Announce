<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

class AnnouncePlugin extends MantisPlugin {
	function register() {
		$this->name = 'Announcements';
		$this->description = '';

		$this->version = '1.0';
		$this->requires		= array(
			'MantisCore' => '1.2.0, <= 1.2.0',
		);

		$this->author		= 'John Reese';
		$this->contact		= 'jreese@leetcode.net';
		$this->url			= 'http://leetcode.net';
	}

	function hooks() {
		return array(
			'EVENT_CORE_READY' => 'api',
			'EVENT_LAYOUT_BODY_BEGIN' => 'body_begin',
			'EVENT_LAYOUT_PAGE_HEADER' => 'body_begin',
		);
	}

	function api() {
		require_once( 'Announce.API.php' );
	}

	function body_begin() {
		Announce::display();
	}
}

