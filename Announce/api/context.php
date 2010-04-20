<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

/**
 * Generate HTML row to be inserted while editing an announcement.
 */
function xmlhttprequest_plugin_announce_add_context() {
	plugin_push_current("Announce");

	$row = gpc_get_int("row");
	$message_id = gpc_get_int("message_id");
?>
<tr class="row-<?php echo $row ?>">
<td class="center">
<a class="announce_delete_context_new" href="#" value="<?php echo $message_id ?>"><img src="<?php echo plugin_file("delete.png") ?>" alt="-" border="0"/></a>
<input type="hidden" name="context_new[]" value="<?php echo $message_id ?>"/></td>
<td class="center"><select name="location_new[]"><?php Announce::print_location_option_list() ?></select></td>
<td class="center"><select name="project_new[]"><?php print_project_option_list() ?></select></td>
<td class="center"><select name="access_new[]"><?php print_enum_string_option_list("access_levels", VIEWER) ?></select></td>
<td class="center"><input name="ttl_new[]" value="0" size="8"/></td>
<td class="center"><input type="checkbox" name="dismissable_new[]" checked="checked"/></td>
</tr>
<?php

	plugin_pop_current();
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
	public static function load_by_id($id) {
		$context_table = plugin_table("context", "Announce");

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");

			if (count($ids) < 1) {
				return array();
			}

			$ids = implode(",", $ids);

			$query = "SELECT * FROM {$context_table} WHERE id IN ({$ids})";
			$result = db_query_bound($query);

			return self::from_db_result($result);

		} else {
			$query = "SELECT * FROM {$context_table} WHERE id=".db_param();
			$result = db_query_bound($query, array($id));

			return array_shift(self::from_db_result($result));
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

