<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

class AnnouncePlugin extends MantisPlugin {
	const VERSION = '2.4.1';

	/**
	 * Error strings
	 */
	const ERROR_DUPLICATE_CONTEXT = 'error_duplicate_context';
	const ERROR_UNKNOWN_LOCATION = 'error_unknown_location';
	const ERROR_INVALID_TTL = 'error_invalid_ttl';

	function register() {
		$this->name = plugin_lang_get("plugin_title");
		$this->description = plugin_lang_get("plugin_description");
		$this->page = 'config_page';

		$this->version = self::VERSION;
		$this->requires		= array(
			"MantisCore" => "2.3.0",
		);

		$this->author		= "John Reese";
		$this->contact		= "jreese@noswap.com";
		$this->url			= "https://github.com/mantisbt-plugins/Announce";
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

			'EVENT_REST_API_ROUTES' => 'routes',
		);
	}

	function errors() {
		return array(
			self::ERROR_DUPLICATE_CONTEXT => plugin_lang_get( self::ERROR_DUPLICATE_CONTEXT ),
			self::ERROR_UNKNOWN_LOCATION => plugin_lang_get( self::ERROR_UNKNOWN_LOCATION ),
			self::ERROR_INVALID_TTL => plugin_lang_get( self::ERROR_INVALID_TTL ),
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
		if( !access_has_global_level( plugin_config_get( "manage_threshold" ) ) ) {
			return '';
		}

		$page = plugin_page( "list" );
		$label = plugin_lang_get( "list_title" );

		return "<a href=\"{$page}\">{$label}</a>";
	}

	function body_begin() {
		Announce::display("header", null, "announcement-header");
	}

	function schema() {
		require_once("api/install.php");

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

			# 2010-04-14
			array( "CreateIndexSQL", array( "idx_plugin_announce_context",
				plugin_table( "context" ), "message_id, project_id, location", array( "UNIQUE" ) ) ),

			# 2014-03-18
			array( 'AddColumnSQL', array( plugin_table( 'dismissed' ),
				"timestamp	I		NOTNULL UNSIGNED DEFAULT 0
				",
				array( "mysql" => "DEFAULT CHARSET=utf8" ) ) ),

			# 2017-10-26 - v2.2.0
			array( 'UpdateFunction', 'delete_orphans_dismissals' ),
		);
	}

	/**
	 * Add the RESTful routes handled by this plugin.
	 *
	 * @param string $p_event_name The event name
	 * @param array  $p_event_args The event arguments
	 * @return void
	 */
	public function routes( $p_event_name, $p_event_args ) {
		$t_app = $p_event_args['app'];
		$t_plugin = $this;
		$t_app->group(
			plugin_route_group(),
			function() use ( $t_app, $t_plugin ) {
				$t_app->post( '/dismiss/{context_id}', [$t_plugin, 'route_dismiss'] );
				$t_app->get( '/context/{message_id}', [$t_plugin, 'route_generate_context_form_fields'] );
			}
		);
	}

	/**
	 * RESTful route for Announcement dismissal.
	 *
	 * Return status
	 * - 200 announcement was successfully dismissed
	 * - 400 invalid context or announcement not dismissable
	 * - 500 dismissal failed (db insert/update error)
	 *
	 * @param Psr\Http\Message\ServerRequestInterface $request
	 * @param Psr\Http\Message\ResponseInterface $response
	 * @param array $args
	 * @return Psr\Http\Message\ResponseInterface
	 */
	public function route_dismiss( $request, $response, $args) {
		$t_timestamp = time();
		$t_user_id = auth_get_current_user_id();

		# Set the reference Context Id for dismissal
		$t_context_id = (int)$args['context_id'];

		# Make sure the message context actually exists
		# and that it can actually be dismissed (i.e. marked as dismissable,
		# or a time-to-live has been set).
		$t_context = AnnounceContext::load_by_id( $t_context_id );
		if( !$t_context ) {
			return $response->withStatus(
				HTTP_STATUS_BAD_REQUEST,
				'Invalid Context Id'
			);
		} elseif( !$t_context->dismissable && $t_context->ttl == 0 ) {
			return $response->withStatus(
				HTTP_STATUS_BAD_REQUEST,
				'Non-dismissible announcement'
			);
		}

		plugin_push_current( $this->basename );
		$t_dismissed_table = plugin_table( 'dismissed' );

		# check for existing dismissal
		$t_query = "SELECT * FROM {$t_dismissed_table}
			WHERE context_id=" . db_param() . "
			AND user_id=" . db_param();
		$t_result = db_query( $t_query,
			array( $t_context_id, $t_user_id )
		);

		if( db_num_rows( $t_result ) < 1 ) {
			$t_query = "INSERT INTO {$t_dismissed_table}
				(context_id, user_id, timestamp)
				VALUES
				(" . db_param() . ", " . db_param() . ", " . db_param(
				) . ")";
			$t_param = array( $t_context_id, $t_user_id, $t_timestamp );
		}
		else {
			$t_query = "UPDATE {$t_dismissed_table}
				SET timestamp = " . db_param() . "
				WHERE context_id=" . db_param() . "
				AND user_id=" . db_param();
			$t_param = array( $t_timestamp, $t_context_id, $t_user_id );
		}
		if( db_query( $t_query, $t_param ) ) {
			$t_status = HTTP_STATUS_SUCCESS;
			$t_msg = '';
		} else {
			$t_status = HTTP_STATUS_INTERNAL_SERVER_ERROR;
			$t_msg = 'Announcement dismissal failed';
		}

		plugin_pop_current();

		return $response->withStatus( $t_status, $t_msg );
	}

	/**
	 * RESTful route to generate HTML for new Announcement context.
	 *
	 * Return status
	 * - 200 HTML was successfully generated
	 * - 400 invalid message
	 *
	 * @param Psr\Http\Message\ServerRequestInterface $request
	 * @param Psr\Http\Message\ResponseInterface $response
	 * @param array $args
	 * @return Psr\Http\Message\ResponseInterface
	 */
	public function route_generate_context_form_fields( $request, $response, $args) {
		$t_message_id = (int)$args['message_id'];

		# Make sure the message actually exists
		$t_message = AnnounceMessage::load_by_id( $t_message_id );
		if( !$t_message ) {
			return $response->withStatus(
				HTTP_STATUS_BAD_REQUEST,
				'Invalid Message Id'
			);
		}

		plugin_push_current( $this->basename );
?>
<tr class="row-context row-new">
	<td class="center">
		<a class="announce_delete_context_new" href="#" data-message-id="<?php echo $t_message_id ?>">
			<img src="<?php echo plugin_file("delete.png") ?>" alt="-" border="0" />
		</a>
		<input type="hidden" name="context_new[]" value="<?php echo $t_message_id ?>"/>
	</td>
	<td class="center"><select name="location_new[]"><?php Announce::print_location_option_list() ?></select></td>
	<td class="center"><select name="project_new[]"><?php print_project_option_list() ?></select></td>
	<td class="center"><select name="access_new[]"><?php print_enum_string_option_list("access_levels", VIEWER) ?></select></td>
	<td class="center">
		<input name="ttl_new[]" class="ttl"
			   title="<?php echo plugin_lang_get("ttl_help") ?>"
			   type="number" value="0" min="0"
		/>
	</td>
	<td class="center"><input type="checkbox" name="dismissable_new[]" checked="checked"/></td>
</tr>
<?php
		plugin_pop_current();

		return $response->withStatus( HTTP_STATUS_SUCCESS );
	}
}
