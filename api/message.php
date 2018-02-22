<?php
# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

/**
 * Announcement Message class
 */
class AnnounceMessage {
	public $id;
	public $timestamp;
	public $title;
	public $message;

	public $contexts = array();

	/**
	 * Target types for messages cleaning operations
	 * @see clean()
	 */
	const TARGET_VIEW = 'view';
	const TARGET_FORM = 'form';

	/**
	 * Create a new message.
	 *
	 * @param string $title Title
	 * @param string $message Message
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
		$timestamp = time();
		# create
		if ($this->id === null) {
			$query = "INSERT INTO {$message_table}
				(
					timestamp,
					title,
					message
				) VALUES (
					" . db_param() . ",
					" . db_param() . ",
					" . db_param() . "
				)";

			db_query($query, array(
				$this->timestamp,
				$this->title,
				$this->message,
			));

			$this->id = db_insert_id($message_table);

		# update
		} else {
			$query = "UPDATE {$message_table} SET
				timestamp=" . db_param() . ",
				title=" . db_param() . ",
				message=" . db_param() . "
				WHERE id=" . db_param();

			db_query($query, array(
				$timestamp,
				$this->title,
				$this->message,
				$this->id
			));
		}

		# save message contexts
		foreach ($this->contexts as $context) {
			$context->message_id = $this->id;
			$context->save();
		}
	}

	/**
	 * Load message objects from the database with the given IDs.
	 *
	 * @param int|array $id Message ID
	 * @param bool $load_contexts
	 * @return AnnounceMessage|array Message object(s)
	 */
	public static function load_by_id($id, $load_contexts=false) {
		$message_table = plugin_table("message", "Announce");

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");

			if (count($ids) < 1) {
				return array();
			}

			$ids = implode(",", $ids);

			$query = "SELECT * FROM {$message_table} WHERE id IN ({$ids}) ORDER BY timestamp DESC";
			$result = db_query($query);

			return self::from_db_result($result, $load_contexts);

		} else {
			$query = "SELECT * FROM {$message_table} WHERE id=".db_param();
			$result = db_query($query, array($id));

			$t_messages = self::from_db_result($result, $load_contexts);
			return array_shift( $t_messages );
		}
	}

	/**
	 * Load a list of all message objects in the database.
	 *
	 * @param bool $load_contexts
	 * @return array Message objects
	 */
	public static function load_all($load_contexts=false) {
		$message_table = plugin_table("message", "Announce");

		$query = "SELECT * FROM {$message_table} ORDER BY timestamp DESC";
		$result = db_query($query);

		return self::from_db_result($result, $load_contexts);
	}

	/**
	 * Load a list of message objects visible to a given user.
	 * Optionally, specify a location or project to further narrow the results.
	 *
	 * @param int $user_id User ID
	 * @param string $location Location name
	 * @param int $project_id Project ID (optional)
	 * @return array Message objects
	 */
	public static function load_visible($user_id, $location, $project_id=0) {
		$context_table = plugin_table("context", "Announce");
		$message_table = plugin_table("message", "Announce");
		$dismissed_table = plugin_table("dismissed", "Announce");

		$query = "SELECT m.*, c.*, c.id AS context_id FROM {$message_table} AS m
			JOIN {$context_table} AS c ON c.message_id=m.id
			LEFT JOIN {$dismissed_table} as d ON d.context_id=c.id and d.user_id=".db_param()."
			WHERE (d.timestamp IS NULL or d.timestamp < m.timestamp)
			AND c.location = ".db_param();

		$params = array($user_id, $location);

		$project_id = (int) $project_id;
		$global_access = (int) access_get_global_level($user_id);

		if ($project_id == ALL_PROJECTS) {
			$query .= " AND c.project_id = ".db_param()."
				AND c.access <= ".db_param();

			$params[] = ALL_PROJECTS;
			$params[] = $global_access;

		} else {
			$query .= " AND (
				(c.project_id = ".db_param()." AND c.access <= ".db_param().")
				OR (c.project_id = ".db_param()." AND c.access <= ".db_param().") )";

			$params[] = ALL_PROJECTS;
			$params[] = $global_access;
			$params[] = $project_id;
			$params[] = (int) access_get_project_level($project_id, $user_id);
		}

		$query .= " ORDER BY m.timestamp DESC";
		$result = db_query($query, $params);

		return self::from_db_result($result, "join");
	}

	/**
	 * Load a list of message objects visible to a given user.
	 * Optionally, specify a location or project to further narrow the results.
	 *
	 * @param int $user_id User ID
	 * @param string $location Location name
	 * @param int $project_id Project ID (optional)
	 * @return AnnounceMessage Message object
	 */
	public static function load_random($user_id, $location, $project_id=0) {
		$visible = self::load_visible($user_id, $location, $project_id);

		if (count($visible) > 0) {
			$message_id = array_rand($visible);
			$message = $visible[$message_id];

			return $message;

		} else {
			return null;
		}
	}

	/**
	 * Generate an array of message objects from a database result.
	 *
	 * @param IteratorAggregate $result Database result
	 * @param bool $load_contexts
	 * @return array Message objects
	 */
	public static function from_db_result($result, $load_contexts=false) {
		$messages = array();

		while($row = db_fetch_array($result)) {
			$message = new AnnounceMessage();
			$message->id = $row["id"];
			$message->timestamp = $row["timestamp"];
			$message->title = $row["title"];
			$message->message = $row["message"];

			if ($load_contexts === "join") {
				$context = new AnnounceContext();
				$context->id = $row["context_id"];
				$context->message_id = $row["message_id"];
				$context->project_id = $row["project_id"];
				$context->location = $row["location"];
				$context->access = $row["access"];
				$context->ttl = $row["ttl"];
				$context->dismissable = $row["dismissable"];

				$message->contexts[] = $context;
			}

			$messages[$message->id] = $message;
		}

		if ($load_contexts === true) {
			$message_ids = array_keys($messages);
			$contexts = AnnounceContext::load_by_message_id($message_ids);

			foreach ($contexts as $context) {
				$messages[$context->message_id]->contexts[$context->id] = $context;
			}
		}

		return $messages;
	}

	/**
	 * Delete messages with the given ID.
	 *
	 * @param int|array $id Message ID
	 */
	public static function delete_by_id($id) {
		$message_table = plugin_table("message");

		AnnounceContext::delete_by_message_id($id);

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");

			if (count($ids) < 1) {
				return;
			}

			$ids = implode(",", $ids);

			$query = "DELETE FROM {$message_table} WHERE id IN ({$ids})";
			db_query($query);

		} else {
			$query = "DELETE FROM {$message_table} WHERE id=".db_param();
			db_query($query, array($id));
		}
	}

	/**
	 * Create a copy of the given object with strings cleaned for output.
	 *
	 * @param array|AnnounceMessage $dirty Message object(s) to clean
	 * @param string $target Target format ('view' or 'form')
	 * @param bool $pattern Apply replacement patterns
	 * @return array|AnnounceMessage Cleaned message object(s)
	 */
	public static function clean($dirty, $target=self::TARGET_VIEW, $pattern=false) {
		if (is_array($dirty)) {
			$cleaned = array();
			foreach ($dirty as $id => $message) {
				$cleaned[$id] = self::clean($message, $target);
			}
			if (false !== $pattern) {
				$cleaned = self::patterns($cleaned);
			}
		} else {
			$cleaned = clone $dirty;

			if( $target == self::TARGET_FORM ) {
				$cleaned->title = string_attribute( $dirty->title );
				$cleaned->message = string_textarea( $dirty->message );
			} else { # self::TARGET_VIEW
				$cleaned->title = string_display_line_links( $dirty->title );
				$cleaned->message = string_display_links( $dirty->message );
			}
		}

		return $cleaned;
	}

	/**
	 * Replace placeholder patterns in the message with appropriate
	 * strings before being sent to the client for usage.
	 *
	 * @param array $messages Message objects
	 * @return array Updated message objects
	 */
	public static function patterns($messages) {
		$current_user = auth_get_current_user_id();
		$username = user_get_name($current_user);

		foreach ($messages as $message) {
			$message->value = str_replace(
				array('%u'),
				array($username),
				$message->value);
		}

		return $messages;
	}

}
