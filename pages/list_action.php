<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

form_security_validate("plugin_Announce_list_action");
access_ensure_global_level(plugin_config_get("manage_threshold"));

$action = gpc_get_string("action");
$message_list = gpc_get_int_array("message_list", array());

if (count($message_list) < 1) {
	form_security_purge("plugin_Announce_list_action");
	print_header_redirect(plugin_page("list", true));
}

$messages = AnnounceMessage::load_by_id($message_list, true);

function array_object_properties($arr, $prop) {
	$props = array();
	foreach($arr as $key => $obj) {
		$props[$key] = $obj->$prop;
	}
	return $props;
}

### EDIT
if ($action == "edit") {
	$messages = AnnounceMessage::clean( $messages, AnnounceMessage::TARGET_FORM );
	layout_page_header(plugin_lang_get("edit_title"));
	layout_page_begin();
?>

<script type="text/javascript" src="<?php echo plugin_file("list_action.js") ?>"></script>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>

<div class="form-container">
<form action="<?php echo plugin_page("list_action_update") ?>" method="post">

	<?php echo form_security_field( 'plugin_Announce_list_action_update' ); ?>
	<input type="hidden" name="action" value="update" />

	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-file-o"></i>
				<?php echo plugin_lang_get( 'edit_title' ); ?>
			</h4>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">

					<div class="widget-toolbox padding-8 clearfix">
						<a class="btn btn-xs btn-primary btn-white btn-round"
						   href="<?php echo plugin_page( 'list' ); ?>">
							<?php echo lang_get( 'back_link' ); ?>
						</a>
					</div>

					<table class="table table-striped table-bordered table-condensed">
						<tbody>
<?php
	$first = true;
	foreach($messages as $message_id => $message) {
		$context_count = count($message->contexts);
		if (!$first) {
?>
							<tr class="spacer"><td></td></tr>
<?php
		}
?>

							<tr>
								<td class="center announce_list_<?php echo $message_id ?>"
									rowspan="<?php echo 3 + $context_count ?>">
									<input type="checkbox" name="message_list[]"
										   value="<?php echo $message_id ?>"
										   checked="checked"/>
								</td>
								<td class="category" colspan="2"><?php echo plugin_lang_get("title") ?></td>
								<td colspan="4">
									<input name="title_<?php echo $message_id ?>"
										   type="text" size="50"
										   value="<?php echo $message->title ?>"
									/>
									<div id="warning_last_context_<?php echo $message_id ?>"
										 class="alert alert-warning warning-last-context">
										<?php echo plugin_lang_get( 'delete_last_context'); ?>
									</div>
								</td>
							</tr>

							<tr>
								<td class="category" colspan="2"><?php echo plugin_lang_get("message") ?></td>
								<td colspan="4">
									<textarea name="message_<?php echo $message_id ?>"
											  cols="70" rows="4"><?php
										echo $message->message
									?></textarea>
								</td>
							</tr>

							<tr class="row-category">
								<td class="category center">
									<a class="announce_add_context" href="#" data-message-id="<?php echo $message_id ?>">
										<img src="<?php echo plugin_file("add.png") ?>" alt="+" border="0"/>
									</a>
								</td>
								<td class="category"><?php echo plugin_lang_get("location") ?></td>
								<td class="category"><?php echo plugin_lang_get("project") ?></td>
								<td class="category"><?php echo plugin_lang_get("access") ?></td>
								<td class="category"><?php echo plugin_lang_get("ttl") ?></td>
								<td class="category"><?php echo plugin_lang_get("dismissable") ?></td>
							</tr>

<?php
		foreach($message->contexts as $context_id => $context) {
?>
							<tr class="row-context">
								<td class="center">
									<a class="announce_delete_context" href="#"
									   data-message-id="<?php echo $message_id ?>"
									   data-context-id="<?php echo $context_id ?>"
									>
										<img src="<?php echo plugin_file("delete.png") ?>"
											 alt="-" border="0"/>
									</a>
									<input type="hidden" name="context_delete_<?php echo $context->id ?>" value="0"/>
								</td>
								<td class="center">
									<select name="location_<?php echo $context->id ?>">
										<?php Announce::print_location_option_list($context->location) ?>
									</select>
								</td>
								<td class="center">
									<select name="project_<?php echo $context->id ?>">
										<?php print_project_option_list($context->project_id) ?>
									</select>
								</td>
								<td class="center">
									<select name="access_<?php echo $context->id ?>">
										<?php print_enum_string_option_list("access_levels", $context->access ) ?>
									</select>
								</td>
								<td class="center">
									<input name="ttl_<?php echo $context->id ?>" class="ttl"
										   title="<?php echo plugin_lang_get("ttl_help") ?>"
										   type="number" value="<?php echo $context->ttl ?>" min="0"
									/>
								</td>
								<td class="center">
									<input type="checkbox" name="dismissable_<?php echo $context->id ?>"
										<?php check_checked((bool)$context->dismissable) ?>/>
								</td>
							</tr>
<?php
		}

		$first = false;
	}
?>
						</tbody>

						<tfoot class="widget-toolbox">
							<tr>
								<td class="center">
									<input type="checkbox" class="announce_select_all" checked="checked"/>
								</td>
								<td class="center" colspan="6">
									<input type="submit" value="<?php echo plugin_lang_get("action_edit") ?>"/>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>
</div>
</div>

<?php
	layout_page_end();
}
