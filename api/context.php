<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

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

		# delete
		if ($this->_delete) {
			AnnounceDismissed::delete_by_context_id($this->id);
			$query = "DELETE FROM {$context_table} WHERE id=".db_param();
			db_query($query, array($this->id));

			return;
		}

		# Make sure there is no existing context for the given location/project
		$query = "SELECT id
			FROM $context_table
			WHERE location = " . db_param() . "
			AND project_id = " . db_param() . "
			AND message_id = " . db_param();
		$param = array(
			$this->location,
			$this->project_id,
			$this->message_id,
		);
		$existing_context = db_result( db_query( $query, $param ) );
		if( $existing_context && ( $this->id === null || $this->id != $existing_context ) ) {
			error_parameters(
				Announce::getLocation( $this->location ),
				project_get_name( $this->project_id )
			);
			plugin_error( AnnouncePlugin::ERROR_DUPLICATE_CONTEXT, ERROR, 'Announce' );
		}

		# create
		if ( $this->id === null ) {
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

			db_query($query, array(
				$this->message_id,
				$this->project_id,
				$this->location,
				$this->access,
				$this->ttl,
				$this->dismissable,
			));

			$this->id = db_insert_id($context_table);

		# update
		} else {
			$query = "UPDATE {$context_table} SET
				project_id=".db_param().",
				location=".db_param().",
				access=".db_param().",
				ttl=".db_param().",
				dismissable=".db_param()."
				WHERE id=".db_param();

			db_query($query, array(
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
	 * @param int|array Message ID (int or array)
	 * @return AnnounceContext|array Context object(s)
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
			$result = db_query($query);

			return self::from_db_result($result);

		} else {
			$query = "SELECT * FROM {$context_table} WHERE id=".db_param();
			$result = db_query($query, array($id));

			$contexts = self::from_db_result($result);
			return reset( $contexts );
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
			$result = db_query($query);

		} else {
			$query = "SELECT * FROM {$context_table} WHERE message_id=".db_param();
			$result = db_query($query, array($message_id));
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

		AnnounceDismissed::delete_by_message_id( $id );

		if (is_array($id)) {
			$ids = array_filter($id, "is_int");

			if (count($ids) < 1) {
				return;
			}

			$ids = implode(",", $ids);

			$query = "DELETE FROM {$context_table} WHERE message_id IN ({$ids})";
			db_query($query);

		} else {
			$query = "DELETE FROM {$context_table} WHERE message_id=".db_param();
			db_query($query, array($id));
		}
	}
}

