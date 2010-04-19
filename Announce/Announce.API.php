<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

function xmlhttprequest_plugin_announce_add_context() {
	$row = gpc_get_int("row");
	$message_id = gpc_get_int("message_id");
?>
<tr class="row-<?php echo $row ?>">
<td class="center">
<a class="announce_delete_context_new" href="#" value="<?php echo $message_id ?>">-<img src="<?php echo plugin_file("delete_context.png") ?>" border="0"/></a>
<input type="hidden" name="context_new[]" value="<?php echo $message_id ?>"/></td>
<td class="center"><select name="location_new[]"><?php Announce::print_location_option_list() ?></select></td>
<td class="center"><select name="project_new[]"><?php print_project_option_list() ?></select></td>
<td class="center"><select name="access_new[]"><?php print_enum_string_option_list("access_levels", VIEWER) ?></select></td>
<td class="center"><input name="ttl_new[]" value="0" size="8"/></td>
<td class="center"><input type="checkbox" name="dismissable_new[]" checked="checked"/></td>
</tr>
<?php
}

class Announce {
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

class AnnounceMessage {
	public $id;
	public $timestamp;
	public $title;
	public $message;

	public $contexts = array();

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

		# save message contexts
		foreach ($this->contexts as $context) {
			$context->message_id = $this->id;
			$context->save();
		}
	}

	/**
	 * Load message objects from the database with the given IDs.
	 *
	 * @param mixed Message ID (int or array)
	 * @return mixed Message object (object or array)
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
			$result = db_query_bound($query);

			return self::from_db_result($result, $load_contexts);

		} else {
			$query = "SELECT * FROM {$message_table} WHERE id=".db_param();
			$result = db_query_bound($query, array($id));

			return array_shift(self::from_db_result($result, $load_contexts));
		}
	}

	/**
	 * Load a list of all message objects in the database.
	 *
	 * @return array Message objects
	 */
	public static function load_all($load_contexts=false) {
		$message_table = plugin_table("message", "Announce");

		$query = "SELECT * FROM {$message_table} ORDER BY timestamp DESC";
		$result = db_query_bound($query);

		return self::from_db_result($result, $load_contexts);
	}

	/**
	 * Load a list of message objects visible to a given user.
	 * Optionally, specify a location or project to further narrow the results.
	 *
	 * @param int User ID
	 * @param string Location name (optional)
	 * @param int Project ID (optional)
	 * @return array Message objects
	 */
	public static function load_visible($user_id, $location="", $project_id=0) {
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
	public static function from_db_result($result, $load_contexts=false) {
		$messages = array();

		while($row = db_fetch_array($result)) {
			$message = new AnnounceMessage();
			$message->id = $row["id"];
			$message->timestamp = $row["timestamp"];
			$message->title = $row["title"];
			$message->message = $row["message"];

			$messages[$message->id] = $message;
		}

		if ($load_contexts) {
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
	 * @param mixed Message ID (int or array)
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
			db_query_bound($query);

		} else {
			$query = "DELETE FROM {$message_table} WHERE id=".db_param();
			db_query_bound($query, array($id));
		}
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
			$cleaned->contexts = $dirty->contexts;
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

class AnnounceContext {
	public $id;
	public $message_id;
	public $project_id;
	public $location;
	public $access;
	public $ttl;
	public $dismissable;

	public $_delete = false;

	/**
	 * Save a new or existing context to the database.
	 */
	public function save() {
		$context_table = plugin_table("context", "Announce");

		# create
		if ($this->id === null && !$this->_delete) {
			$query = "INSERT INTO {$context_table}
				(
					message_id,
					project_id,
					location,
					access,
					ttl,
					dismissable
				) VALUES (
					".db_param().",
					".db_param().",
					".db_param().",
					".db_param().",
					".db_param().",
					".db_param()."
				)";

			db_query_bound($query, array(
				$this->message_id,
				$this->project_id,
				$this->location,
				$this->access,
				$this->ttl,
				$this->dismissable,
			));

			$this->id = db_insert_id($context_table);

		# delete
		} elseif ($this->_delete) {
			$query = "DELETE FROM {$context_table} WHERE id=".db_param();
			db_query_bound($query, array($this->id));

		# update
		} else {
			$query = "UPDATE {$context_table} SET
				project_id=".db_param().",
				location=".db_param().",
				access=".db_param().",
				ttl=".db_param().",
				dismissable=".db_param()."
				WHERE id=".db_param();

			db_query_bound($query, array(
				$this->project_id,
				$this->location,
				$this->access,
				$this->ttl,
				$this->dismissable,
				$this->id,
			));
		}
	}

	/**
	 * Load context objects from the database for the given message IDs.
	 *
	 * @param mixed Message ID (int or array)
	 * @return array Context objects (object or array)
	 */
	public static function load_by_message_id($message_id) {
		$context_table = plugin_table("context", "Announce");

		if (is_array($message_id)) {
			$ids = array_filter($message_id, "is_int");

			if (count($ids) < 1) {
				return array();
			}

			$ids = implode(",", $ids);

			$query = "SELECT * FROM {$context_table} WHERE message_id IN ({$ids})";
			$result = db_query_bound($query);

		} else {
			$query = "SELECT * FROM {$context_table} WHERE message_id=".db_param();
			$result = db_query_bound($query, array($message_id));
		}

		return self::from_db_result($result);
	}

	/**
	 * Generate an array of context objects from a database result.
	 *
	 * @param object Database result
	 * @return array Context objects
	 */
	public static function from_db_result($result) {
		$contexts = array();

		while($row = db_fetch_array($result)) {
			$context = new AnnounceContext();
			$context->id = $row["id"];
			$context->message_id = $row["message_id"];
			$context->project_id = $row["project_id"];
			$context->location = $row["location"];
			$context->access = $row["access"];
			$context->ttl = $row["ttl"];
			$context->dismissable = $row["dismissable"];

			$contexts[$context->id] = $context;
		}

		return $contexts;
	}

	/**
	 * Delete contexts for the given Message ID.
	 *
	 * @param mixed Message ID (int or array)
	 */
	public static function delete_by_message_id($id) {
		$context_table = plugin_table("context");

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");

			if (count($ids) < 1) {
				return;
			}

			$ids = implode(",", $ids);

			$query = "DELETE FROM {$context_table} WHERE message_id IN ({$ids})";
			db_query_bound($query);

		} else {
			$query = "DELETE FROM {$context_table} WHERE message_id=".db_param();
			db_query_bound($query, array($id));
		}
	}
}

