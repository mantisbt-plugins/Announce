<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

class Announce {
	/**
	 * Generate the HTML for displaying (and potentially dismissing) an announcement.
	 * A div element is created with the CSS class "announcement", which can optionally
	 * contain other classes as well for extra CSS styling.  For dismissable contexts,
	 * an image is added that hooks to an AJAX call to dismiss the announcement.
	 *
	 * @param string Location name
	 * @param int Project ID (optional)
	 * @param string CSS class
	 */
	public static function display($location, $project_id=null, $css_class="") {
		if (auth_is_user_authenticated()) {
			if ($project_id === null) {
				$project_id = helper_get_current_project();
			}

			$message = AnnounceMessage::load_random(auth_get_current_user_id(), "header", $project_id);

			if ($message !== null) {
				$css_class = string_attribute($css_class);
				$message = AnnounceMessage::clean($message, AnnounceMessage::TARGET_VIEW);
				$context = array_shift($message->contexts);

				$html = sprintf(
					'<span><strong>%s</strong></span><br/><span class="announcement-msg">%s<span>' . "\n",
					$message->title,
					$message->message
				);

				if ($context->dismissable) {
					$html = sprintf(
							'<img class="announcement-dismiss" src="%s" alt="Dismiss Announcement" value="%d"/>',
							plugin_file("dismiss.png"),
							$context->id
						)
						. "\n" . $html;
				}

				printf(
					'<div class="announcement noprint %s" data-id="%d" data-ttl="%d">%s</div>',
					$css_class,
					$context->id, $context->ttl,
					$html
				);
				echo "\n";
			}
		}
	}

	/**
	 * Generate a list of available announcement locations.
	 *
	 * @return array Location names
	 */
	public static function locations() {
		static $locs = null;

		if ($locs !== null) {
			return $locs;
		}

		$locs = array(
			"header" => plugin_lang_get("location_header", "Announce"),
		);

		return $locs;
	}

	/**
	 * Generate HTML dropdown options for the list of available locations.
	 * @param string $value Default value
	 * @return void
	 */
	public static function print_location_option_list($value=null) {
		if ($value === null) {
			echo '<option value="">', plugin_lang_get("select_one", "Announce"), '</option>';
		}

		foreach(Announce::locations() as $loc => $locname) {
			echo "<option value=\"{$loc}\"";
			check_selected( $loc, (string)$value );
			echo ">{$locname}</option>\n";
		}
	}
}

