<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

class AnnounceMessage {
	public $id;
	public $timestamp;
	public $title;
	public $message;

	/**
	 * Create a new message.
	 *
	 * @param string Title
	 * @param string Message
	 */
	public function __construct($title="", $message="") {
		$this->timestamp = time();
		$this->title = $title;
		$this->message = $message;
	}

	/**
	 * Save a new or existing message to the database.
	 */
	public function save() {
		$message_table = plugin_table("message", "Announce");

		# create
		if ($this->id === null) {
			$query = "INSERT INTO {$message_table}
				(
					timestamp,
					title,
					message
				) VALUES (
					".db_param().",
					".db_param().",
					".db_param()."
				)";

			db_query_bound($query, array(
				$this->timestamp,
				$this->title,
				$this->message,
			));

			$this->id = db_insert_id($message_table);

		# update
		} else {
			$query = "UPDATE {$message_table} SET
				timestamp=".db_param().",
				title=".db_param().",
				message=".db_param()."
				WHERE id=".db_param();

			db_query_bound($query, array(
				$this->timestamp,
				$this->title,
				$this->message,
				$this->id
			));
		}
	}

	/**
	 * Load message objects from the database with the given IDs.
	 *
	 * @param mixed Message ID (int or array)
	 * @return mixed Message object (object or array)
	 */
	public static function load_by_id($id) {
		$message_table = plugin_table("message", "Announce");

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");
			$ids = implode(",", $ids);

			$query = "SELECT * FROM {$message_table} WHERE id IN ({$ids}) ORDER BY timestamp DESC";
			$result = db_query_bound($query);

			return self::from_db_result($result);

		} else {
			$query = "SELECT * FROM {$message_table} WHERE id=".db_param();
			$result = db_query_bound($query, array($id));

			return array_shift(self::from_db_result($result));
		}
	}

	/**
	 * Load a list of all message objects in the database.
	 *
	 * @return array Message objects
	 */
	public static function load_all() {
		$message_table = plugin_table("message", "Announce");

		$query = "SELECT * FROM {$message_table} ORDER BY timestamp DESC";
		$result = db_query_bound($query);

		return self::from_db_result($result);
	}

	/**
	 * Load a list of message objects visible to a given user.
	 * Optionally, specify a block or project to further narrow the results.
	 *
	 * @param int User ID
	 * @param string Block name (optional)
	 * @param int Project ID (optional)
	 * @return array Message objects
	 */
	public static function load_visible($user_id, $block="", $project_id=0) {
		$message_table = plugin_table("message", "Announce");

		/* todo: update query to pay attention to display and dismissed tables */
		$query = "SELECT * FROM {$message_table} ORDER BY timestamp DESC";
		$result = db_query_bound($query);

		return self::from_db_result($result);
	}

	/**
	 * Generate an array of message objects from a database result.
	 *
	 * @param object Database result
	 * @return array Message objects
	 */
	public static function from_db_result($result) {
		$messages = array();
		while($row = db_fetch_array($result)) {
			$message = new AnnounceMessage();
			$message->id = $row["id"];
			$message->timestamp = $row["timestamp"];
			$message->title = $row["title"];
			$message->message = $row["message"];

			$messages[$message->id] = $message;
		}
		return $messages;
	}

	/**
	 * Create a copy of the given object with strings cleaned for output.
	 *
	 * @param object Message object
	 * @param string Target format
	 * @param boolean Replacement patterns
	 * @return object Cleaned message object
	 */
	public static function clean($dirty, $target="view", $pattern=false) {
		if (is_array($dirty)) {
			$cleaned = array();
			foreach ($dirty as $id => $message) {
				$cleaned[$id] = self::clean($message, $target);
			}
			if (false !== $pattern) {
				$cleaned = self::patterns($cleaned, $pattern);
			}

		} else {
			if ($target == "view") {
				$title = string_display_line($dirty->title);
				$message = string_display($dirty->message);

			} elseif ($target == "form") {
				$title = string_attribute($dirty->title);
				$message = string_textarea($dirty->message);
			}

			$cleaned = new AnnounceMessage($title, $message);
			$cleaned->id = $dirty->id;
			$cleaned->timestamp = $dirty->timestamp;
		}

		return $cleaned;
	}

	/**
	 * Replace placeholder patterns in the message with appropriate
	 * strings before being sent to the client for usage.
	 *
	 * @param array Message objects
	 * @return array Updated message objects
	 */
	public static function patterns($messages) {
		$current_user = auth_get_current_user_id();
		$username = user_get_name($current_user);

		foreach ($messages as $messages) {
			$message->value = str_replace(
				array('%u'),
				array($username),
				$message->value);
		}

		return $messages;
	}

}
