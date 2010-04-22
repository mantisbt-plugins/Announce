<?php

# Copyright (c) 2010 John Reese
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
				$message = AnnounceMessage::clean($message, "links");
				$context = array_shift($message->contexts);

				$html = "<span><strong>{$message->title}</strong><br/>{$message->message}<span>";

				if ($context->dismissable) {
					$image = plugin_file("dismiss.png");
					$html = "<img class=\"announcement-dismiss\" src=\"{$image}\" alt=\"Dismiss Announcement\" value=\"{$context->id}\"/>{$html}";
				}

				echo "<div class=\"announcement {$css_class}\">{$html}</div>";
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
	 */
	public static function print_location_option_list($value=null) {
		if ($value === null) {
			echo '<option value="">', plugin_lang_get("select_one", "Announce"), '</option>';
		}

		foreach(Announce::locations() as $loc => $locname) {
			$selected = check_selected($loc, $value);
			echo "<option value=\"{$loc}\" {$selected}>{$locname}</option>";
		}
	}
}

